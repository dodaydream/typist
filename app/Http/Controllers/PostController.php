<?php

namespace App\Http\Controllers;
use App\Posts;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getPosts()
    {
        return Posts::paginate();
    }

    public function getPostById($id)
    {
        $post = Posts::find($id);
        if ($post) {
            return response()->json($post);
        }
        abort(404, 'Post Not Found');
    }

    public function createPost(Request $request)
    {
        $post = json_decode($request->data, true);
        if (Posts::create($post))
            return response()->json(['created' => true]);
    }

    public function updatePost(Request $request, $id)
    {
        $newPost = json_decode($request->data, true);
        $post = Posts::find($id);
        if ($post){
            if ($post->update($newPost)) {
                return response()->json(['updated' => true]);
            }
            abort(500, "The post can't be updated.");
        }
        abort(404, 'Post Not Found');
    }

    public function deletePost($id)
    {
        $post = Posts::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['deleted' => true]);
        }
        abort(404, 'Post Not Found');
    }

    public function recoverPost($id)
    {
        $post = Posts::onlyTrashed()->where('id', $id)->get();
        if ($post) {
            $post->restore();
            return response()->json(['restored' => true]);
        }
        abort(404, 'Post Not Found');
    }

    public function getTrashedPosts()
    {
        $posts = Posts::onlyTrashed()->get();
    }
}
