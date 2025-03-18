<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;


class ReactionController extends Controller
{
    public function createReaction(Request $req)
    {
        $has = $this ->checkLoginUserIsThumbUp($req->ideaId, $req->userId);
        try {
            if($has) {
                $reaction = Reaction::where('idea_id', $req->ideaId)->where('user_id', $req->userId)->first();
                if (!$reaction) {
                    return ApiResponseClass::sendResponse(null, 'Reaction not found', 404);
                }

                $reaction->update([
                    'isThumbUp' => $req->isThumbUp,
                    'remark' => $req->remark
                ]);
            }
            else {
                $reaction = Reaction::create([
                    'user_id' => $req->userId,
                    'idea_id' => $req->ideaId,
                    'isThumbUp' => $req->isThumbUp,
                    'remark' => $req->remark
                ]);
            }

            return ApiResponseClass::sendResponse($reaction, 'Success', 200);
        } 
        catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Fail');
        }
        
    }

    public function readReactions()
    {
        try {
            $reactions = Reaction::all();
            return ApiResponseClass::sendResponse($reactions, 'Success',200);
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Fail');
        }
    }

    public function readReactionByIdeaId($ideaId)
    {
        try {
            $reaction = Reaction::where('idea_id', $ideaId) -> get();

            if (!$reaction) {
                return ApiResponseClass::sendResponse(null, 'Reaction not found', 404);
            }

            return ApiResponseClass::sendResponse($reaction, 'Success',200);
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Fail');
        }
    }

    // public function deleteReactionById($id)
    // {
    //     try {
    //         $reaction = Reaction::find($id);

    //         if (!$reaction) {
    //             return ApiResponseClass::sendResponse(null, 'Reaction not found', 404);
    //         }

    //         $reaction->delete();

    //         return ApiResponseClass::sendResponse($reaction, 'Success');
    //     } catch (\Exception $err) {
    //         return ApiResponseClass::rollback($err, 'Fail');
    //     }
    // }

    public function getTotalLike($ideaId)
    {
        try {
            $totalLike = Reaction::where('idea_id', $ideaId)->where('isThumbUp', 'like')->count();
            return ApiResponseClass::sendResponse($totalLike, 'Success',200);
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Fail');
        }
    }

    public function getTotalUnLike($ideaId)
    {
        try {
            $totalUnlike = Reaction::where('idea_id', $ideaId)->where('isThumbUp', 'unlike')->count();
            return ApiResponseClass::sendResponse($totalUnlike, 'Success',200);
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Fail');
        }
    }

    //customize function
    public function checkLoginUserIsThumbUp($ideaId, $userId)
    {
        try {
            return Reaction::where('idea_id', $ideaId)
                ->where('user_id', $userId)
                ->exists();
        } catch (\Exception $err) {
            return false; 
        }
    }
}
