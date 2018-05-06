<?php

namespace App\Http\Controllers;
use App\Posts;
use App\Revisions;
use App\Users;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function getRevisionsByPostId(int $post_id)
    {
        $post = Posts::find($post_id);
        if ($post)
        {
            $revisions = $post->revisions()->select(['id', 'user_id', 'created_at'])->orderBy('created_at', 'desc')->get();

            if ($revisions)
            {
                foreach ($revisions as $revision)
                {
                    $revision['user_name'] = $revision->author->name;
                }

                return response()->json($revisions);
            }

            return response()->json([]);
        }

        abort(404, 'Post not found');
    }

    public function getRevisionByPostId(int $post_id, int $rev_id)
    {
        $post = Posts::find($post_id);
        if ($post)
        {
            $revision = $post->revisions()->where('id', $rev_id)->first();
            if ($revision)
            {
                $revision['created_by'] = $revision->author->name;
                return response()->json($revision);
            }
            abort(404, 'Revision not found');
        }
        abort(404, 'Post not found');
    }

    public function rollbackRevision(int $post_id, int $rev_id)
    {
        $post = Posts::find($post_id);
        if ($post) {
            $revision = $post->revisions->where('id', $rev_id)->first();
            if ($revision) {
                $post->revision_id = $revision->id;
                $post->save();
                $revision->save();
                return response()->json(PostController::makePost($post));
            }
            abort(404, 'Revision not in this post');
        }
        abort(404, 'Post not found');
    }

    public function deleteRevision(int $post_id, int $rev_id)
    {
        $post = Posts::find($post_id);
        if ($post) {
            $revisions = $post->revisions();
            // get revision
            $revision = $revisions->where('id', $rev_id)->first();
            if ($revision) {
                // if is current revision
                if ($revision->id != $post->revision_id) {
                    $revision->delete();
                    return response()->json(['msg' => 'deleted']);
                }

                abort(405, 'Cannot delete current active revision');
            }
            abort(404, 'Revision not in this post');
        }
        abort(404, 'Post not found');
    }
}
