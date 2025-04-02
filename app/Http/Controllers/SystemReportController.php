<?php

namespace App\Http\Controllers;
use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemReportController extends Controller
{
    public function getTopActiveUserByAcademicYear($academicId)
    {
        try{
            $result = DB::table('users')
            ->leftJoin('ideas', function ($join) use ($academicId) {
                $join->on('ideas.user_id', '=', 'users.id')
                     ->where('ideas.academic_year_id', '=', $academicId);
            })
            ->leftJoin('comments', 'comments.user_id', '=', 'users.id')
            ->select(
                'users.id as userId',
                'users.user_name as userName',
                'users.email',
                DB::raw('COUNT(DISTINCT ideas.id) as ideaCount'),
                DB::raw('COALESCE(SUM(ideas.view_count), 0) as viewCount'),
                DB::raw('COUNT(DISTINCT comments.id) as commentCount')
            )
            ->groupBy('users.id', 'users.user_name', 'users.email')
            ->orderByRaw('(COUNT(DISTINCT ideas.id) + COALESCE(SUM(ideas.view_count), 0) + COUNT(DISTINCT comments.id)) DESC')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->viewCount = (int) $user->viewCount;
                return $user;
            });


            if (count($result) == 0) {
                return ApiResponseClass::sendResponse(null, 'No data found', 404);
            }
            return ApiResponseClass::sendResponse($result, 'Success', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Fail', 500);
        }
    }

    public function getSystemReportCounts($academicYearId)
    {
        try {
            $countObj = [];
            $commentCount = DB::table('comments')
                ->join('ideas', 'comments.idea_id', '=', 'ideas.id')
                ->where('ideas.academic_year_id', $academicYearId)
                ->count();
            $ideaCount = DB::table('ideas')
                ->where('ideas.academic_year_id', $academicYearId)
                ->count();
            $departmentCount = DB::table('departments')
                ->count();
            $countObj['commentCount'] = $commentCount;
            $countObj['ideaCount'] = $ideaCount;
            $countObj['departmentCount'] = $departmentCount;
            return ApiResponseClass::sendResponse($countObj, 'System Report Count successfully retrieved.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, "Exception while getting system report counts!");
        }
    }

    public function getAnonymousCountsByAcademicYearForManager($academicYearId)
    {
        try {
            $countObj = [];

            $validator = Validator::make(['academic_year_id' => $academicYearId], [
                'academic_year_id' => 'required|exists:academic_years,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid academic year ID'], 422);
            }

            $anonymousIdeaCount = Idea::where('academic_year_id', $academicYearId)
                ->where('is_anonymous', true)
                ->count();

            $anonymousCommentCount = Comment::join('ideas', 'comments.idea_id', '=', 'ideas.id')
                ->where('ideas.academic_year_id', $academicYearId)
                ->where('comments.is_anonymous', true)
                ->count();

            $countObj = [
                'anonymousIdeaCount' => $anonymousIdeaCount,
                'anonymousCommentCount' => $anonymousCommentCount
            ];

            return ApiResponseClass::sendResponse($countObj, 'Annonymous counts successfully retrieved.', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, "Exception while annonymous getting system report counts!");
        }
    }
}
