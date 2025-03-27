<?php

namespace App\Http\Controllers;
use App\Classes\ApiResponseClass;
use App\Models\Idea;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SystemReportController extends Controller
{
    public function getTopActiveUserByDepartment($departmentId)
    {
        try{
            $result = DB::table('users')
                ->leftJoin('ideas', 'ideas.user_id', '=', 'users.id')
                ->leftJoin('comments', 'comments.user_id', '=', 'users.id')
                ->select(
                    'users.id as userId',
                    'users.user_name as userName',
                    'users.email',
                    DB::raw('COUNT(DISTINCT ideas.id) as ideaCount'),
                    DB::raw('COALESCE(SUM(ideas.view_count), 0) as viewCount'),
                    DB::raw('COUNT(DISTINCT comments.id) as commentCount')
                )
                ->where('users.department_id', $departmentId)
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
       
}
