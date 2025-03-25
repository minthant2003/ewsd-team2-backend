<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\IdeaDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class IdeaController extends Controller
{
    public function submitIdea(Request $request){
        try {
            // Check current academic year first
            $currentAY = AcademicYearController::getCurrentAcademicYear();
            if (!$currentAY) {
                return ApiResponseClass::sendResponse(
                    null,
                    'Some limitations concerning Academic Year. Pleasee contact the Admin.',
                    300);
            }

            $validationFailObj = $this->validateAddRequest($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }

            $formattedData = $this->formatRequestForDb($request);
            $submittedIdea = Idea::create($formattedData); // snake case

            // deal with files
            $uploadedFiles = $request->file('files');
            if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    $filePath = $file->store('idea_documents', 'public'); // store in /storage/app/public/idea_documents
                    IdeaDocument::create([ // store file path in idea_document
                        'file_name' => $filePath,
                        'public_file_url' => Storage::url($filePath),
                        'idea_id' => $submittedIdea->id,
                        'remark' => null,
                    ]);
                }
            }

            $submittedIdea->load('ideaDocuments'); // load related idea docs
            $camelCaseObj = $this->getIdeaWithDocsInCamelCase($submittedIdea);
            return ApiResponseClass::sendResponse($camelCaseObj, 'Idea submitted successfully.', 200);
        } catch(\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to submit an idea.');
        }
    }

    public function getIdeaById($id)
    {
        try {
            $ideaWithDocs = Idea::with("ideaDocuments")->find($id);
            if (!$ideaWithDocs) {
                return ApiResponseClass::sendResponse(null, 'Idea not found', 404);
            }
            $camelObj = $this->getIdeaWithDocsInCamelCase($ideaWithDocs);
            return ApiResponseClass::sendResponse($camelObj, 'Idea fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Idea.');
        }
    }

    public function getIdeas(Request $request)
    {
        try {
            $resData = [];
            $paginateObj = null;
            $camelList = [];
            
            // Get sort parameter from request
            $sortBy = $request->input('sortBy', 'created_at');
            
            // Map frontend sort parameters to database column names
            $sortColumnMap = [
                'createdAt' => 'created_at',
                'popularity' => 'popularity',
            ];
            
            // Get the actual column name to sort by
            $sortColumn = $sortColumnMap[$sortBy] ?? 'created_at';
            
            // Get category filter from request
            $categoryId = $request->input('categoryId');
            
            // Get keyword search from request
            $keyword = $request->input('keyword');
            
            // Get page from request
            $page = $request->input('page', 1);
            
            $query = Idea::with('ideaDocuments')
                ->join('users', 'ideas.user_id', '=', 'users.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->leftJoin('comments', 'ideas.id', '=', 'comments.idea_id')
                ->select(
                    'ideas.*',
                    'users.user_name as user_name',
                    'categories.category_name as category_name',
                    DB::raw('COUNT(DISTINCT comments.id) as comments_count')
                )
                ->groupBy('ideas.id', 'users.user_name', 'categories.category_name');
            
            // Apply category filter if provided
            if ($categoryId && $categoryId !== 'all') {
                $query->where('ideas.category_id', $categoryId);
            }
            
            // Apply keyword search if provided
            if ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('ideas.title', 'like', '%' . $keyword . '%')
                      ->orWhere('ideas.content', 'like', '%' . $keyword . '%');
                });
            }
            
            $ideas = $query->orderBy($sortColumn, 'desc')
                ->paginate(5, ['*'], 'page', $page);
                
            $paginateObj = $this->getPaginateObj($ideas);
            foreach ($ideas->items() as $idea) {
                $ideaData = $this->getIdeaWithDocsInCamelCase($idea);
                $ideaData['userName'] = $idea->user_name;
                $ideaData['categoryName'] = $idea->category_name;
                $ideaData['commentsCount'] = $idea->comments_count;
                $camelList[] = $ideaData;
            }
            $resData = [
                'pagination' => $paginateObj,
                'ideaList' => $camelList,
            ];
            return ApiResponseClass::sendResponse($resData, 'Idea List has been successfully retrieved.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Ideas.');
        }
    }

    public function deleteIdeaById($id)
    {
        try {
            $idea = Idea::with('ideaDocuments')->find($id);
            if (!$idea) {
                return ApiResponseClass::sendResponse(null, 'Idea not found.', 404);
            }
            foreach ($idea->ideaDocuments as $doc) {
                // Step 1. delete file from storage
                if (Storage::exists("public/{$doc->file_name}")) {
                    Storage::delete("public/{$doc->file_name}");
                }
            }
            // Step 2. delete from idea_documents
            $idea->ideaDocuments()->delete();
            // Step 3. delete from idea
            $idea->delete();
            return ApiResponseClass::sendResponse(null, "Idea deleted successfully.", 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete Idea.');
        }
    }

    private function validateAddRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'userId' => 'required|exists:users,id',
            'categoryId' => 'required|exists:categories,id',
            'files' => 'sometimes|nullable|array', // accepts undefined or NULL or [] (optional -> CW requirement)
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2097152' // max file size is 2GB
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
        }
        return null;
    }

    private function formatRequestForDb($request)
    {
        return [
            "title" => $request->title,
            "content" => $request->content,
            "is_anonymous" => filter_var($request->isAnonymous, FILTER_VALIDATE_BOOLEAN),
            "view_count" => 0,
            "popularity" => 0,
            "user_id" => $request->userId,
            "category_id" => $request->categoryId,
            "academic_year_id" => AcademicYearController::getCurrentAcademicYear()->id, // get current AY id
            "remark" => $request->remark ?? null,
        ];
    }

    private function getIdeaWithDocsInCamelCase($ideaWithDocs)
    {
        // Get reaction counts using direct queries
        $totalLikes = DB::table('reactions')
            ->where('idea_id', $ideaWithDocs->id)
            ->where('reaction', 'like')
            ->count();
            
        $totalUnlikes = DB::table('reactions')
            ->where('idea_id', $ideaWithDocs->id)
            ->where('reaction', 'unlike')
            ->count();

        return [
            'id' => $ideaWithDocs->id,
            'title' => $ideaWithDocs->title,
            'content' => $ideaWithDocs->content,
            'isAnonymous' => (bool) $ideaWithDocs->is_anonymous,
            'viewCount' => $ideaWithDocs->view_count,
            'popularity' => $ideaWithDocs->popularity,
            'userId' => $ideaWithDocs->user_id,
            'categoryId' => $ideaWithDocs->category_id,
            'academicYearId' => $ideaWithDocs->academic_year_id,
            'remark' => $ideaWithDocs->remark,
            'createdAt' => Carbon::parse($ideaWithDocs->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($ideaWithDocs->updated_at)->format('Y-m-d H:i:s'),
            'ideaDocuments' => $ideaWithDocs->ideaDocuments->map(fn($doc) => [
                'id' => $doc->id,
                'fileName' => $doc->file_name,
                'publicFileUrl' => $doc->public_file_url,
                'ideaId' => $doc->idea_id,
                'remark' => $doc->remark,
                'createdAt' => Carbon::parse($doc->created_at)->format('Y-m-d H:i:s'),
                'updatedAt' => Carbon::parse($doc->updated_at)->format('Y-m-d H:i:s'),
            ]),
            "reportCount" => $ideaWithDocs->report_count,
            "totalLikes" => $totalLikes,
            "totalUnlikes" => $totalUnlikes,
        ];
    }

    private function getPaginateObj($pagination)
    {
        return [
            'currentPage' => $pagination->currentPage(),
            'lastPage' => $pagination->lastPage(),
            'perPage' => $pagination->perPage(),
            'total' => $pagination->total(),
            'firstPageUrl' => $pagination->url(1),
            'lastPageUrl' => $pagination->url($pagination->lastPage()),
            'nextPageUrl' => $pagination->nextPageUrl(),
            'prevPageUrl' => $pagination->previousPageUrl(),
            'from' => $pagination->firstItem(),
            'to' => $pagination->lastItem(),
        ];
    }

    public function reportIdea($id)
    {
        $idea = Idea::find($id);
        $idea->report_count++;
        $idea->save();
    }

    public function increaseViewCount($id)
    {
        try {
            $idea = Idea::find($id);
            if (!$idea) {
                return ApiResponseClass::sendResponse(null, 'Idea not found.', 404);
            }
            $idea->view_count++;
            $idea->save();
            return ApiResponseClass::sendResponse(null, 'View count increased successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to increase view count.');
        }
    }

    public function getIdeasByUserId(Request $request, $userId)
    {
        try {
            $resData = [];
            $paginateObj = null;
            $camelList = [];
            
            // Get sort parameter from request
            $sortBy = $request->input('sortBy', 'created_at');
            
            // Map frontend sort parameters to database column names
            $sortColumnMap = [
                'createdAt' => 'created_at',
                'popularity' => 'popularity',
            ];
            
            // Get the actual column name to sort by
            $sortColumn = $sortColumnMap[$sortBy] ?? 'created_at';
            
            // Get category filter from request
            $categoryId = $request->input('categoryId');
            
            // Get keyword search from request
            $keyword = $request->input('keyword');
            
            // Get page from request
            $page = $request->input('page', 1);
            
            $query = Idea::with('ideaDocuments')
                ->join('users', 'ideas.user_id', '=', 'users.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->leftJoin('comments', 'ideas.id', '=', 'comments.idea_id')
                ->select(
                    'ideas.*',
                    'users.user_name as user_name',
                    'categories.category_name as category_name',
                    DB::raw('COUNT(DISTINCT comments.id) as comments_count')
                )
                ->where('ideas.user_id', $userId)
                ->groupBy('ideas.id', 'users.user_name', 'categories.category_name');
            
            // Apply category filter if provided
            if ($categoryId && $categoryId !== 'all') {
                $query->where('ideas.category_id', $categoryId);
            }
            
            // Apply keyword search if provided
            if ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('ideas.title', 'like', '%' . $keyword . '%')
                      ->orWhere('ideas.content', 'like', '%' . $keyword . '%');
                });
            }
            
            $ideas = $query->orderBy($sortColumn, 'desc')
                ->paginate(5, ['*'], 'page', $page);
                
            $paginateObj = $this->getPaginateObj($ideas);
            foreach ($ideas->items() as $idea) {
                $ideaData = $this->getIdeaWithDocsInCamelCase($idea);
                $ideaData['userName'] = $idea->user_name;
                $ideaData['categoryName'] = $idea->category_name;
                $ideaData['commentsCount'] = $idea->comments_count;
                $camelList[] = $ideaData;
            }
            $resData = [
                'pagination' => $paginateObj,
                'ideaList' => $camelList,
            ];
            return ApiResponseClass::sendResponse($resData, 'User Ideas List has been successfully retrieved.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch User Ideas.');
        }
    }

    public function getIdeaWithComments($id)
    {
        try {
            $idea = Idea::with(['ideaDocuments'])
                ->join('users', 'ideas.user_id', '=', 'users.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->select('ideas.*', 'users.user_name', 'categories.category_name')
                ->where('ideas.id', $id)
                ->first();

            if (!$idea) {
                return ApiResponseClass::sendResponse(null, 'Idea not found', 404);
            }

            // Get comments with user information
            $comments = DB::table('comments')
                ->join('users', 'comments.user_id', '=', 'users.id')
                ->where('comments.idea_id', $id)
                ->select(
                    'comments.*',
                    'users.user_name',
                    'users.id as user_id'
                )
                ->orderBy('comments.created_at', 'desc')
                ->get()
                ->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'desc' => $comment->desc,
                        'userId' => $comment->user_id,
                        'userName' => $comment->user_name,
                        'isAnonymous' => (bool) $comment->is_anonymous,
                        'createdAt' => Carbon::parse($comment->created_at)->format('Y-m-d H:i:s'),
                        'updatedAt' => Carbon::parse($comment->updated_at)->format('Y-m-d H:i:s'),
                    ];
                });

            $ideaData = $this->getIdeaWithDocsInCamelCase($idea);
            $ideaData['userName'] = $idea->user_name;
            $ideaData['categoryName'] = $idea->category_name;
            $ideaData['comments'] = $comments;

            return ApiResponseClass::sendResponse($ideaData, 'Idea details fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch idea details.');
        }
    }
}
