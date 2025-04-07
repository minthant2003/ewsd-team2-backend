<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Classes\ApiResponseClass;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use App\Services\NotiMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommentController extends Controller
{
    // Add a new comment
    public function addComment(Request $request, $ideaId)
    {
        try {
            // Validate request
            $validationFailObj = $this->commentValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }

            // Check if idea exists
            $idea = Idea::find($ideaId);
            if (!$idea) {
                return ApiResponseClass::sendResponse(null, 'Idea not found.', 404);
            }

            // Prepare comment data
            $data = $this->requestCommentData($request, $ideaId);
            $comment = Comment::create($data);

            // send email notification
            $this->sendNotiToAuthor($request, $ideaId);

            $camelObj = $this->formatCamelCase($comment);
            return ApiResponseClass::sendResponse($camelObj, 'Comment added successfully.', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to add comment.');
        }
    }

    // Retrieve all comments for a specific idea
    public function getCommentsByIdea($ideaId)
    {
        try {
            $comments = Comment::where('idea_id', $ideaId)
                ->with(['user' => function ($query) {
                    $query->select('id', 'user_name');
                }])
                ->latest()
                ->get()
                ->map(function ($comment) {
                    if ($comment->is_anonymous) {
                        $comment->user_id = null;
                        $comment->user = null;
                    }
                    return $this->formatCamelCase($comment);
                });

            return ApiResponseClass::sendResponse($comments, 'Comments retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch comments.');
        }
    }

    // Delete a comment
    public function deleteComment($id)
    {
        try {
            $comment = Comment::find($id);
            if (!$comment) {
                return ApiResponseClass::sendResponse(null, 'Comment not found.', 404);
            }

            // Admins can delete any comment, users can only delete their own
            if ($comment->is_anonymous) {
                if (Auth::user()->role !== 'admin') {
                    return ApiResponseClass::sendResponse(null, 'Unauthorized action.', 403);
                }
            } else {
                if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
                    return ApiResponseClass::sendResponse(null, 'Unauthorized action.', 403);
                }
            }

            $comment->delete();
            return ApiResponseClass::sendResponse(null, 'Comment deleted successfully.');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete comment.');
        }
    }

    private function sendNotiToAuthor($request, $ideaId)
    {
        $commentedBy = null;
        $writerId = $request->userId;
        $writer = User::find($writerId);
        $author = null;
        $idea = Idea::find($ideaId);
        if ($writer && $idea) {
            $authorId = $idea->user_id;
            $author = User::find($authorId);
            $commentedBy = filter_var($request->isAnonymous, FILTER_VALIDATE_BOOLEAN)
                ? "Anonymous"
                : $writer->user_name;
            if ($author) {
                $commentedAt = Carbon::now()->format('Y-m-d H:i:s');
                $toEmail = $author->email;
                $subject = "New Comment on Your Idea";
                $msg = "A new comment has been made. [Idea Title - {$idea->title}, Commented by - {$commentedBy}, Commented at - {$commentedAt}].";
                NotiMailService::sendNotiMail($toEmail, $subject, $msg);
            }
        }
    }

    // Validate comment data
    private function commentValidationCheck($request)
    {
        $validator = Validator::make($request->all(), [
            'desc' => 'required|string',
            'is_anonymous' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), 'Validation errors', 400);
        }
        return null;
    }

    // Format request data for comment creation
    private function requestCommentData($request, $ideaId)
    {
        return [
            'desc' => $request->desc,
            'is_anonymous' => filter_var($request->isAnonymous, FILTER_VALIDATE_BOOLEAN),
            'user_id' => $request->is_anonymous ? null : Auth::id(),
            'idea_id' => $ideaId,
        ];
    }

    // Format response to use camel case
    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'desc' => $obj->desc,
            'isAnonymous' => (bool) $obj->is_anonymous,
            'userId' => $obj->user_id,
            'ideaId' => $obj->idea_id,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
