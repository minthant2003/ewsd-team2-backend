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
                ->select('reported_ideas.*', 'ideas.user_id as idea_user_id')
                ->get();
            $camelObjList = [];
            foreach ($reportedIdeas as $reportedIdea) {
                $camelObjList[] = $this->formatCamelCase($reportedIdea);
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
                ->select('reported_ideas.*', 'ideas.user_id as idea_user_id')
                ->where('reported_ideas.user_id', $userId)
                ->get();

            if ($reportedIdeas->isEmpty()) {
                return ApiResponseClass::sendResponse(null, 'No reported ideas found for this user.', 404);
            }

            $camelObjList = [];
            foreach ($reportedIdeas as $reportedIdea) {
                $camelObjList[] = $this->formatCamelCase($reportedIdea);
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
