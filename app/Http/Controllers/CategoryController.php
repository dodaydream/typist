<?php

namespace App\Http\Controllers;
use App\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
    /**
     * @api {get} /categories List all categories
     * @apiName GetCategories
     * @apiGroup Categories
     *
     * @apiSuccess {json} List of categories
     */
    public function listCategories()
    {
        $categories = Categories::all();
        $resp = $categories->toArray();
        return response()->json($resp);
    }

    /**
     * @api {post} /categories Create a new category
     * @apiName GetCategories
     * @apiGroup Categories
     *
     * @apiSuccess {json} List of categories
     */
    public function createCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories'
        ]);

        try {
            if ($newCategory = Categories::create($request->all())) {
                return response()->json($newCategory);
            }
        } catch (Exception $e) {
            return response()->json('Category not created');
        }
    }

    public function updateCategory(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories'
        ]);

        $category = Categories::find($id);

        if ($category) {

            $category->name = $request->input('name');

            try {
                $category->save();
                return response()->json($category);
            } catch (Exception $e) {
                abort(500, "The category can't be updated.");
            }
        } else {
            abort(404, 'Category not found.');
        }
    }

    // TODO
    public function deleteCategory(int $id)
    {
        $category = Categories::find($id);
        if ($category) {
            $posts = $category->posts;
            $delete = \DB::transaction(function () use ($category, $posts) {
                foreach ($posts as $post) {
                    $post->category_id = 0;
                    $post->save();
                }

                $category->delete();
            });
            if (is_null($delete))
                return response()->json(['code' => 202, 'msg' => 'Category deleted', 'posts_affected' => $posts]);
            return response()->json(['code' => 400, 'msg' => 'An error occured during deletion']);
        }
        abort(404, 'Category Not Found');
    }
}
