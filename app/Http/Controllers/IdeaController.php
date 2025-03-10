<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\IdeaDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                        'idea_id' => $submittedIdea->id,
                        'remark' => null,
                    ]);
                }
            }

            $submittedIdea->load('ideaDocuments'); // load idea docs
            $camelCaseObj = $this->getIdeaWithDocsInCamelCase($submittedIdea);
            return ApiResponseClass::sendResponse($camelCaseObj, 'Idea submitted successfully.', 200);
        } catch(\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to submit an idea.');
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
            "is_anonymous" => $request->isAnonymous ?? false,
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
            'createdAt' => $ideaWithDocs->created_at,
            'updatedAt' => $ideaWithDocs->updated_at,
            'ideaDocuments' => $ideaWithDocs->ideaDocuments->map(fn($doc) => [
                'id' => $doc->id,
                'fileName' => $doc->file_name,
                'ideaId' => $doc->idea_id,
                'remark' => $doc->remark,
                'createdAt' => $doc->created_at,
                'updatedAt' => $doc->updated_at,
            ]),
        ];
    }
}
