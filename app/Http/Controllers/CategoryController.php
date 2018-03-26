<?php

namespace App\Http\Controllers;
use App\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
	/**
	 * Return an json containing all categories.
	 *
	 * @Param void
	 * @return json
	 */
    public function listCategories()
    {
        $categories = Categories::all();
		$resp = $categories->toArray();
        return response()->json($resp);
    }

	/**
	 * Create a new category.
	 *
	 * @param category name
	 * @return json
	 */
    public function createCategory(Request $request)
    {
		$category = $request->all();
		if (!isset($category['name']))
			abort(400, 'Category name cannot be null');
		if ($newCategory = Categories::create($category)) {
			return response()->json($newCategory);
		}
	}

    public function updateCategory(Request $request, int $id)
    {
        $category = Categories::find($id);
        if ($category) {
			$newCategory = $request->all();
			if (!isset($newCategory['name']))
				abort(400, 'Category name cannot be null');
			$category->name = $newCategory['name'];
            if ($category->save()) {
                return response()->json($category);
            }
            abort(500, "The category can't be updated.");
        }
        abort(404, 'Category not found.');
    }

	// TODO
    public function deleteCategory(int $id)
    {
        $category = Categories::find($id);
        if ($category) {
			$delete = \DB::transaction(function () use ($category) {
				$posts = $category->posts();
				foreach ($posts as $post) {
					$post->category_id = null;
					$post->save();
				}

				$category->delete();
			});
			if (is_null($delete))
				return response()->json(['code' => 202, 'msg' => 'Category deleted']);
			return response()->json(['code' => 400, 'msg' => 'An error occured during deletion']);
        }
        abort(404, 'Category Not Found');
    }
}
