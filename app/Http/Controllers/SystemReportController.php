<?php

namespace App\Http\Controllers;
use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\User;
use App\Models\Comment;
use Carbon\Carbon;
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
                return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
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
            return ApiResponseClass::rollback($e, "Exception while getting system report anonymous counts!");
        }
    }

    public function getContributorByDepartment($academicYearId)
    {
        try {
            $validator = Validator::make(['academic_year_id' => $academicYearId], [
                'academic_year_id' => 'required|exists:academic_years,id'
            ]);

            if ($validator->fails()) {
                return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
            }

            $contributors = User::join('departments', 'users.department_id', '=', 'departments.id')
                ->leftJoin('ideas', function ($join) use ($academicYearId) {
                    $join->on('users.id', '=', 'ideas.user_id')
                        ->where('ideas.academic_year_id', $academicYearId);
                })
                ->leftJoin('comments', function ($join) use ($academicYearId) {
                    $join->on('users.id', '=', 'comments.user_id')
                        ->join('ideas as idea_for_comment', 'comments.idea_id', '=', 'idea_for_comment.id')
                        ->where('idea_for_comment.academic_year_id', $academicYearId);
                })
                ->leftJoin('reactions', function ($join) use ($academicYearId) {
                    $join->on('users.id', '=', 'reactions.user_id')
                        ->join('ideas as idea_for_reaction', 'reactions.idea_id', '=', 'idea_for_reaction.id')
                        ->where('idea_for_reaction.academic_year_id', $academicYearId);
                })
                ->select(
                    'departments.id as departmentId',
                    'departments.department_name as departmentName',
                    DB::raw('COUNT(DISTINCT users.id) as contributorCount')
                )
                ->where(function ($query) {
                    $query->whereNotNull('ideas.id')
                        ->orWhereNotNull('comments.id')
                        ->orWhereNotNull('reactions.id');
                })
                ->groupBy('departments.id', 'departments.department_name')
                ->get();

            return ApiResponseClass::sendResponse([
                'academicYearId' => $academicYearId,
                'academicName' => DB::table('academic_years')->where('id', $academicYearId)->value('academic_name'),
                'data' => $contributors
            ], 'Contributors by department fetched successfully');

        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, "Exception while fetching contributors by department.");
        }
    }

    public function getMostViewedIdeas($academicYearId)
    {
        try {
            $validator = Validator::make(['academic_year_id' => $academicYearId], [
                'academic_year_id' => 'required|exists:academic_years,id'
            ]);

            if ($validator->fails()) {
                return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
            }

            $mostViewedIdeas = Idea::where('academic_year_id', $academicYearId)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();

            $camelObjList = [];
            foreach ($mostViewedIdeas as $mostViewedIdea) {
                $camelObjList[] = $this->formatCamelCaseForIdea($mostViewedIdea);
            }

            return ApiResponseClass::sendResponse($camelObjList, 'Most viewed ideas fetched successfully');

        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, "Exception while fetching most viewed ideas.");
        }
    }

    private function formatCamelCaseForIdea($obj)
    {
        return [
            'id' => $obj->id,
            'title' => $obj->title,
            'content' => $obj->content,
            'isAnonymous' => (bool) $obj->is_anonymous,
            'viewCount' => $obj->view_count,
            'popularity' => $obj->popularity,
            'userId' => $obj->user_id,
            'categoryId' => $obj->category_id,
            'academicYearId' => $obj->academic_year_id,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
