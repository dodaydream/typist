<?php

namespace App\Http\Controllers;
use App\Posts;
use App\Categories;
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

	/**
	 * Get paginated posts.
	 *
	 * @param int $page
	 * @return json
	 */
    public function getPosts(int $page)
    {
        $posts = Posts::skip(($page - 1) * 10)->take(10)->get();
        return response()->json(['code' => 200, 'page' => $page, 'posts' => $posts]);
    }

	/**
	 * Get Posts By CategoryId.
	 *
	 * @param int $categoryId
	 * @param int $page
	 *
	 * @return json
	 */
    public function getPostsByCategoryId(int $categoryId, int $page)
    {
        $posts = Categories::find($categoryId);
        if ($posts) {
            $posts = $posts->posts()->skip(($page - 1) * 10)->take(10)->get();
            return response()->json(['code' => 200, 'page' => $page, 'posts' => $posts]);
        }
        abort(404, 'Category not exist');
    }

    public function getPostById(int $id)
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

    public function updatePost(Request $request, int $id)
    {
        $post = Posts::find($id);
        if ($post) {
			$data = json_decode($response);
			if ($revision = Revision::create($data)) {
				$post->revision_id = $revision->id;
				$post->save();
			}
			return response()->json(['code' => 201, 'msg' => 'Post upgraded']);
        }
        abort(404, 'Post Not Found');
    }

    public function deletePost(int $id)
    {
        $post = Posts::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['deleted' => true]);
				
        }
        abort(404, 'Post Not Found');
    }

    public function restorePost(int $id)
    {
        $post = Posts::onlyTrashed()->where('id', $id);
        if ($post) {
            $post->restore();
            return response()->json(['restored' => true]);
        }
        abort(404, 'Post Not Found');
    }

    public function getTrashedPosts(int $page)
    {
        $posts = Posts::onlyTrashed()->skip(($page - 1) * 10)->take(10)->get();
        if (empty($posts))
            return response()->json(['page' => $page, 'posts' => $posts]);
        abort(404, 'Page Doesn\'t exist');
    }

    public function getTrashedPostById(int $id)
    {
        $post = Posts::onlyTrashed()->where('id', $id)->get();
        if ($post) {
            return response()->json($post);
        }
        abort(404, 'Post Not Found');
    }

	public function removeTrashedPost(int $id)
	{
	}

	public function removeTrashedPosts(int $id)
	{
	}
}
