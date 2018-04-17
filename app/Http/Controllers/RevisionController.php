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
        $revisions = $post->revisions()->orderBy('id', 'desc')->get();
        foreach ($revisions as $revision)
        {
            $revision['user_name'] = Users::find($revision['user_id'])->name;
        }
        return response()->json($revisions);
    }

    public function rollbackRevision(int $post_id, int $rev_id)
    {
        $post = Posts::find($post_id);
        if ($post) {
            $revision = $post->revisions->where('id', $rev_id)->first();
            if ($revision) {
                $post->revision_id = $revision->id;
                $post->save();
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
            $revisions = $post->revisions;
            $revision = $revisions->where('id', $rev_id)->first();
            if ($revision) {
                $revision->delete();
                if ($revision->id == $post->revision_id) {
                    $revision_id = $revisions->orderBy('created_at', 'desc')->first()->id;
                    $this->rollbackRevision($post_id, $revision_id);
                }
            }
            abort(404, 'Revision not in this post');
        }
        abort(404, 'Post not found');
    }
}
