<?php

namespace App\Http\Controllers;
use App\Posts;
use App\Comments;
use Illuminate\Http\Request;
use GeoIp2\Database\Reader;

class CommentController extends Controller
{
	private static function getLocation(string $ip)
	{
		$reader = new Reader('../database/GeoLite2-City.mmdb');
		try {
			$record = $reader->city($ip);
		} catch (\GeoIp2\Exception\AddressNotFoundException $e) {
		}

		return isset($record) ? ($record->city->name.', '.$record->country->name) : 'Unknown';
	}

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
			$comments->location = Self::getLocation($comments->commenter_ip);

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
                foreach ($comments as $comment) {
					$comment->location = Self::getLocation($comment->commenter_ip);
                }
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
