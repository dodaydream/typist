<?php

namespace App\Http\Controllers;

use App\Posts;
use App\Categories;
use App\Revisions;
use App\PostComments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
    public function listPosts(Request $request, int $page=1)
    {
        $offset = ($page - 1) * self::POST_PER_PAGE;
        $filter_flag = false;
        $filter = [];

        if (null !== $request->query('category'))
        {
            $id = filter_var($request->query('category'), FILTER_VALIDATE_INT);

            if ($id == 0) {
                // uncategorized posts
                $posts = Posts::where('category_id', 0);
            } else if ($category = Categories::find($id)) {
                // categorized posts
                $posts = $category->posts();
            } else {
                abort(404, 'Category not found');
            }

            $filter['category_id'] = $id;
            $filter_flag = true;
        }

        if (null !== $request->query('expand'))
        {
            $expand_flag = filter_var($request->query('expand'), FILTER_VALIDATE_BOOLEAN);
            if ($filter_flag) {
                $posts = $posts->where('expand_content', $expand_flag);
            } else {
                $posts = Posts::where('expand_content', $expand_flag);
                $filter_flag = true;
            }

            $filter['expanded_post'] = $expand_flag;
        }

        if ($filter_flag)
        {
            if ($posts) {
                $count = $posts->count();
                $posts = $posts->skip($offset)->orderBy('updated_at', 'desc')->take(self::POST_PER_PAGE)->get();
            }
        }
        else
        {
            // all posts
            $count = Posts::count();
            $posts = Posts::skip($offset)->orderBy('updated_at', 'desc')->take(self::POST_PER_PAGE)->get();
        }

        if ($posts)
        {
            foreach ($posts as $post) {
                $post->last_edit_by = $post->revision->author->name;
                $post->user_id = $post->revision->user_id;
                if ($post->category_id != 0)
                    $post->category_name = $post->category->name;
                else
                    $post->category_name = 'Uncatagorized';
                if ($post->expand_content)
                    $post->content = $post->revision->content;

                if ($post->password_protected)
                    $post->content = "This post is password protected";
            }

            $resp = [
                'page' => $page,
                'count' => $count,
                'filter' => $filter,
                'posts' => $posts
            ];

            return response()->json($resp);
        }

        abort(404, 'Posts not fonud');
    }

    public function retrievePost(int $id)
    {
        $post = Posts::find($id);
        if ($post->password_protected)
            abort(401, 'You don\'t have the permission for this post');

        if ($post && $post = Self::makePost($post))
            return response()->json($post);

        abort(404, 'Post Not Found');
    }

    public function retrieveProtectedPost(Request $request, int $id)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);

        $postCredential = PostsPassword::where('post_id', $id)->first();
        if (Hash::check($request->password, $postCredential->password)) {
            return response()->json(Self::makePost(Posts::find($id)));
        }

        abort(401, 'Invalid credential');
    }

    public function createPost(Request $request)
    {
        $post = $request->all();
        if (isset($post['expand_content']) && $post['expand_content'] == false && !isset($post['title']))
            abort(400, "Missing title");

        $hasPassword = $request->has('password') ? true : false;

        $postData = [
            'title' => $request->has('title') ? $post['title'] : '',
            'category_id' => $request->has('cagtegory_id') ? $post['category_id'] : 0,
            'expand_content' => $request->has('expand_content') ? $post['expand_content'] : false,
            'password_protected' => $hasPassword
        ];

        $revisionData = [
            'content' => $request->input('content'),
            'user_id' => $request->user()
        ];

        if ($hasPassword) {
            $passwordData = [
                'password' => Hash::make($request->input('password'))
            ];
        }

        \DB::transaction(function () use (&$post, &$postData, &$revisionData, &$passwordData, &$hasPassword) {
            $post = Posts::create($postData);
            $revision = $post->revisions()->create($revisionData);
            if ($hasPassword) {
                $password = $post->password()->create($passwordData);
            }
            $post['revision_id'] = $revision->id;
            $post->save();
        });

        $resp = [
            'id' => $post->id,
            'category_id' => $post->category_id,
            'title' => $post['title'],
            'user_id' => $revisionData['user_id'],
            'revision_id' => $post['revision_id'],
            'content' => $post->revision->content,
            'expand_content' => $post['expand_content'],
            'password_protected' => $hasPassword
        ];

        return response()->json($resp);
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
                if (isset($req['expand_content']))
                    $post->expand_content = $req['expand_content'];
                $post->save();
            });

            $post = Posts::find($id);
            return response()->json(Self::makePost($post));
        }
        abort(404, 'Post Not Found');
    }

    public function updatePassword(Request $request, int $id)
    {
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

    public static function deleteRevisions(Posts $post)
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
            $post->category_name = $post->category_id ? $post->category->name : 'Uncategorized';
            $post->last_edit_by = $post->revision->author->name;
            $post->content = $post->revision->content;
            return $post;
        }
    }
}
