<?php

namespace App\Http\Controllers;
use App\Posts;
use App\Comments;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function createCommentByPostId(int $id, Request $request)
    {
        $post = Posts::find($id);
        if ($post)
        {
            $comment = [
                'commenter_ip' => $request->ip(),
                'content' => $request->content
            ];

            $comments = $post->comments()->create($comment);

            return response()->json($comments);
        }

        abort(404, 'Post Not Found');
    }

    public function deleteComment(int $id)
    {
        $comment = Comments::find($id);
        if ($comment)
        {
            $comment->delete();
            return response()->json(['message' => 'deleted']);
        }

        abort(404, 'Comment not found');
    }

    public function retriveComments(int $page, int $id)
    {
        $offset = ($page - 1) * 10;
        if ($id)
        {
            $post = Posts::find($id);
            if ($post)
            {
                $comments = $post->comments();
                $count = $comments->count();
                $comments = $comments->orderBy('created_at', 'desc')
                             ->skip($offset)->take(10)->get();
            } else {
                abort(404, 'Post not found');
            }
        } else {
            $count = Comment::count();
            $comments = Comments::skip($offset)->take(10)->get();
        }

        return response()->json(['count' => $count, 'comments' => $comments]);
    }
}
