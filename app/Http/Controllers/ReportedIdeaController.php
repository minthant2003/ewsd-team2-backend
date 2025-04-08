<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\ReportedIdea;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportedIdeaController extends Controller
{
    public function createReportedIdea(Request $request, IdeaController $ideaController){
        try {
            $validationFailObj = $this->reportedIdeaValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }

            $exists = ReportedIdea::where('user_id', $request->userId)
                            ->where('idea_id', $request->ideaId)
                            ->exists();

            if ($exists) {
                return ApiResponseClass::sendResponse(null, 'This idea has already been reported.', 409);
            }

            $idea = Idea::find($request->ideaId);
            if (!$idea) {
                return ApiResponseClass::sendResponse(null, 'Idea not found.', 404);
            }

            $data = $this->formatReportedIdeaForDb($request);
            $reportedIdea = ReportedIdea::create($data);
            $ideaController->reportIdea($request->ideaId);

            $camelObj = $this->formatCamelCase($reportedIdea);
            return ApiResponseClass::sendResponse($camelObj, 'Idea reported successfully.', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to report idea.');
        }
    }

    public function getReportedIdeas(){
        try {
            $reportedIdeas = ReportedIdea::join('ideas', 'reported_ideas.idea_id', '=', 'ideas.id')
                ->join('users as idea_authors', 'ideas.user_id', '=', 'idea_authors.id')
                ->join('users as reporters', 'reported_ideas.user_id', '=', 'reporters.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->select(
                    'reported_ideas.*',
                    'ideas.title',
                    'ideas.content',
                    'ideas.is_anonymous',
                    'ideas.view_count',
                    'ideas.popularity',
                    'ideas.report_count',
                    'ideas.is_hidden',
                    'ideas.created_at as idea_created_at',
                    'ideas.updated_at as idea_updated_at',
                    'idea_authors.id as author_id',
                    'idea_authors.user_name as author_name',
                    'idea_authors.email as author_email',
                    'idea_authors.department_id as author_department_id',
                    'idea_authors.is_disable as author_is_disable',
                    'reporters.id as reporter_id',
                    'reporters.user_name as reporter_name',
                    'reporters.email as reporter_email',
                    'categories.id as category_id',
                    'categories.category_name'
                )
                ->get();
                
            $camelObjList = [];
            foreach ($reportedIdeas as $reportedIdea) {
                $camelObjList[] = [
                    'id' => $reportedIdea->id,
                    'userId' => $reportedIdea->user_id,
                    'ideaId' => $reportedIdea->idea_id,
                    'createdAt' => Carbon::parse($reportedIdea->created_at)->format('Y-m-d H:i:s'),
                    'updatedAt' => Carbon::parse($reportedIdea->updated_at)->format('Y-m-d H:i:s'),
                    'idea' => [
                        'id' => $reportedIdea->idea_id,
                        'title' => $reportedIdea->title,
                        'content' => $reportedIdea->content,
                        'isAnonymous' => (bool) $reportedIdea->is_anonymous,
                        'viewCount' => $reportedIdea->view_count,
                        'popularity' => $reportedIdea->popularity,
                        'reportCount' => $reportedIdea->report_count,
                        'isHidden' => (bool) $reportedIdea->is_hidden,
                        'createdAt' => Carbon::parse($reportedIdea->idea_created_at)->format('Y-m-d H:i:s'),
                        'updatedAt' => Carbon::parse($reportedIdea->idea_updated_at)->format('Y-m-d H:i:s'),
                        'categoryId' => $reportedIdea->category_id,
                        'categoryName' => $reportedIdea->category_name
                    ],
                    'author' => $reportedIdea->is_anonymous ? [
                        'id' => $reportedIdea->author_id,
                        'userName' => 'Anonymous',
                        'email' => null,
                        'departmentId' => $reportedIdea->author_department_id,
                        'isDisable' => $reportedIdea->author_is_disable
                    ] : [
                        'id' => $reportedIdea->author_id,
                        'userName' => $reportedIdea->author_name,
                        'email' => $reportedIdea->author_email,
                        'departmentId' => $reportedIdea->author_department_id,
                        'isDisable' => $reportedIdea->author_is_disable
                    ],
                    'reporter' => [
                        'id' => $reportedIdea->reporter_id,
                        'userName' => $reportedIdea->reporter_name,
                        'email' => $reportedIdea->reporter_email
                    ]
                ];
            }
            return ApiResponseClass::sendResponse($camelObjList, 'Reported Ideas fetched successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch reported ideas.');
        }
    }

    public function getReportedIdeasByUserId($userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'This user does not exist.', 404);
            }

            $reportedIdeas = ReportedIdea::join('ideas', 'reported_ideas.idea_id', '=', 'ideas.id')
                ->join('users as idea_authors', 'ideas.user_id', '=', 'idea_authors.id')
                ->join('users as reporters', 'reported_ideas.user_id', '=', 'reporters.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->select(
                    'reported_ideas.*',
                    'ideas.title',
                    'ideas.content',
                    'ideas.is_anonymous',
                    'ideas.view_count',
                    'ideas.popularity',
                    'ideas.report_count',
                    'ideas.is_hidden',
                    'ideas.created_at as idea_created_at',
                    'ideas.updated_at as idea_updated_at',
                    'idea_authors.id as author_id',
                    'idea_authors.user_name as author_name',
                    'idea_authors.email as author_email',
                    'idea_authors.department_id as author_department_id',
                    'reporters.id as reporter_id',
                    'reporters.user_name as reporter_name',
                    'reporters.email as reporter_email',
                    'categories.id as category_id',
                    'categories.category_name'
                )
                ->where('reported_ideas.user_id', $userId)
                ->get();

            if ($reportedIdeas->isEmpty()) {
                return ApiResponseClass::sendResponse(null, 'No reported ideas found for this user.', 404);
            }

            $camelObjList = [];
            foreach ($reportedIdeas as $reportedIdea) {
                $camelObjList[] = [
                    'id' => $reportedIdea->id,
                    'userId' => $reportedIdea->user_id,
                    'ideaId' => $reportedIdea->idea_id,
                    'createdAt' => Carbon::parse($reportedIdea->created_at)->format('Y-m-d H:i:s'),
                    'updatedAt' => Carbon::parse($reportedIdea->updated_at)->format('Y-m-d H:i:s'),
                    'idea' => [
                        'id' => $reportedIdea->idea_id,
                        'title' => $reportedIdea->title,
                        'content' => $reportedIdea->content,
                        'isAnonymous' => (bool) $reportedIdea->is_anonymous,
                        'viewCount' => $reportedIdea->view_count,
                        'popularity' => $reportedIdea->popularity,
                        'reportCount' => $reportedIdea->report_count,
                        'isHidden' => (bool) $reportedIdea->is_hidden,
                        'createdAt' => Carbon::parse($reportedIdea->idea_created_at)->format('Y-m-d H:i:s'),
                        'updatedAt' => Carbon::parse($reportedIdea->idea_updated_at)->format('Y-m-d H:i:s'),
                        'categoryId' => $reportedIdea->category_id,
                        'categoryName' => $reportedIdea->category_name
                    ],
                    'author' => $reportedIdea->is_anonymous ? [
                        'id' => $reportedIdea->author_id,
                        'userName' => 'Anonymous',
                        'email' => null,
                        'departmentId' => $reportedIdea->author_department_id
                    ] : [
                        'id' => $reportedIdea->author_id,
                        'userName' => $reportedIdea->author_name,
                        'email' => $reportedIdea->author_email,
                        'departmentId' => $reportedIdea->author_department_id
                    ],
                    'reporter' => [
                        'id' => $reportedIdea->reporter_id,
                        'userName' => $reportedIdea->reporter_name,
                        'email' => $reportedIdea->reporter_email
                    ]
                ];
            }

            return ApiResponseClass::sendResponse($camelObjList, 'Reported ideas fetched successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch reported ideas.');
        }
    }

    public function deleteReportedIdea($id){
        try {
            $reportedIdea = ReportedIdea::find($id);
            
            if (!$reportedIdea) {
                return ApiResponseClass::sendResponse(null, 'Reported idea not found.', 404);
            }
            $idea = Idea::find($reportedIdea->idea_id);
            $idea->report_count--;
            $idea->save();

            $reportedIdea->delete();

            return ApiResponseClass::sendResponse(null, 'Reported idea deleted successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete reported idea.');
        }
    }

    private function reportedIdeaValidationCheck($request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:users,id',
            'ideaId' => 'required|exists:ideas,id',
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), 'Validation errors', 400);
        }
        return null;
    }

    private function formatReportedIdeaForDb($request)
    {
        return [
            'user_id' => $request->userId,
            'idea_id' => $request->ideaId,
        ];
    }
    
    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'userId' => $obj->user_id,
            'ideaId' => $obj->idea_id,
            'ideaUserId' => $obj->idea_user_id,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
