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
    public function getCategories()
    {
        $categories = Categories::all();
		$data = [
			'code' => 200,
			'data' => $categories->toArray()
		];
        return response()->json($data);
    }

	/**
	 * Create a new category.
	 *
	 * @param category name
	 * @return json
	 */
    public function createCategory(Request $request)
    {
		if (empty($request->name))
			abort(400, 'Category name cannot be null');
		$category = new Categories();
		$category->name = $request->name;
		if ($category->save())
            return response()->json(['code' => 201, 'data' => 'created']);
    }

    public function updateCategory(Request $request, int $id)
    {
        $category = Categories::find($id);
        if ($category) {
			$newCategory = $request;
            if ($category->update($newCategory)) {
                return response()->json(['updated' => true]);
            }
            abort(500, "The category can't be updated.");
        }
        abort(404, 'Category not found.');
    }

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
