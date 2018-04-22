<?php

namespace App\Http\Controllers;
use App\Posts;
use App\Categories;
use App\Revisions;
use App\PostComments;
use Illuminate\Http\Request;

class PostController extends Controller
{
    const POST_PER_PAGE = 10;
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
    public function listPosts(int $page=1, string $filter=null, int $id=null)
    {
        $offset = ($page - 1) * self::POST_PER_PAGE;
        if ($filter == 'category') {
            // uncategorized posts
            if ($id == 0)
                $posts = Posts::where('category_id', 0);
            // categorized posts
            else
                $posts = Categories::find($id)->posts();
            $count = $posts->count();
            $posts = $posts->skip($offset)->take(self::POST_PER_PAGE)->get();
        } else {
            // all posts
            $count = Posts::count();
            $posts = Posts::skip($offset)->take(self::POST_PER_PAGE)->get();
        }

        foreach($posts as $post) {
            $post->last_edit_by = $post->revision->author->name;
            if ($post->category_id != 0)
                $post->category_name = $post->category->name;
            else
                $post->category_name = 'Uncatagorized';
        }

        $resp = [
            'page' => $page,
            'count' => $count,
            'filter_by' => $filter,
            'filter_id' => $id,
            'posts' => $posts
        ];

        return response()->json($resp);
    }

    public function retrivePost(int $id)
    {
        $post = Posts::find($id);
        if ($post && $post = Self::makePost($post))
            return response()->json($post);
        abort(404, 'Post Not Found');
    }

    public function createPost(Request $request)
    {
        $post = $request->all();
        if (!isset($post['title']))
            abort(400, "Missing title");

        $postData = [
            'title' => $post['title'],
            'category_id' => $post['category_id']
        ];

        $revisionData = [
            'content' => $post['content'],
            'user_id' => $request->user()
        ];

        \DB::transaction(function () use (&$post, &$postData, &$revisionData) {
            $post = Posts::create($postData);
            $revision = $post->revisions()->create($revisionData);
            $post['revision_id'] = $revision->id;
            $post->save();
        });

        $resp = [
            'id' => $post['id'],
            'category_id' => $post['category_id'],
            'title' => $post['title'],
            'user_id' => $revisionData['user_id'],
            'revision_id' => $post['revision_id'],
            'content' => $post['content']
        ];

        return response()->json($resp);
        if (Posts::create($post))
            return response()->json(['created' => true]);
    }

    public function updatePost(Request $request, int $id)
    {
        $post = Posts::find($id);
        if ($post) {
            $req = $request->all();
            \DB::transaction(function () use (&$post, &$req, $request) {
                if (isset($req['content']) && $req['content'] != $post->revision->content) {
                    $revision = [
                        'content' => $req['content'],
                        'user_id' => $request->user()
                    ];

                    $revision = $post->revisions()->create($revision);

                    if ($revision) {
                        $revision_id = $revision->id;
                        $post->revision_id = $revision_id;
                    }
                }

                if (isset($req['title']))
                    $post->title = $req['title'];
                if (isset($req['category_id']))
                    $post->category_id = $req['category_id'];
                $post->save();
            });

            $post = Posts::find($id);
            return response()->json(Self::makePost($post));
        }
        abort(404, 'Post Not Found');
    }

    public function deletePost(int $id)
    {
        $post = Posts::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['status' => 'deleted']);
        }
        abort(404, 'Post Not Found');
    }

    public function restoreTrashedPost(int $id)
    {
        $post = Posts::onlyTrashed()->where('id', $id);
        if ($post) {
            $post->restore();
            return response()->json(['status' => 'restored']);
        }
        abort(404, 'Post Not Found');
    }

    public function retriveTrashedPost(int $id)
    {
        $post = Posts::onlyTrashed()->where('id', $id)->first();
        if ($post) {
            return response()->json(Self::makePost($post));
        }
        abort(404, 'Post Not Found');
    }

    public function deleteTrashedPost(int $id)
    {
        $post = Posts::onlyTrashed()->where('id', $id)->first();
        if ($post) {
            Self::deleteRevisions($post);
            return response()->json(['status' => 'Permanately Deleted']);
        }
        abort(404, 'Post Not Found');
    }

    public static function deleteRevisions(Post $post)
    {
        \DB::transaction(function () use ($post) {
            $post->revisions()->delete();
            $post->forceDelete();
        });
    }

    public function listTrashedPosts(int $page)
    {
        $posts = Posts::onlyTrashed()
            ->skip(($page - 1) * self::POST_PER_PAGE)->take(self::POST_PER_PAGE)->get();

        foreach($posts as $post) {
            $post->last_edit_by = $post->revision->author->name;
            if ($post->category_id != 0)
                $post->category_name = $post->category->name;
            else
                $post->category_name = 'Uncatagorized';
        }

        if (!empty($posts))
            return response()->json(['page' => $page, 'posts' => $posts]);
        abort(404, 'Page Doesn\'t exist');
    }

    public static function makePost(Posts $post)
    {
        if ($post) {
            return [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'category' => $post->category != null ? $post->category->name : null,
                'title' => $post->title,
                'last_edit_by' => $post->revision->author->name,
                'updated_at' => $post->updated_at,
                'revision_id' => $post->revision_id,
                'content' => $post->revision->content
            ];
        }
    }
}
