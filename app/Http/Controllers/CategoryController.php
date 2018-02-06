<?php

namespace App\Http\Controllers;
use App\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $categories = Categories::all();
        return response()->json($categories);
    }

    public function createCategory(Request $request)
    {
        $category = json_decode($request->data, true);
        if (Categories::create($post))
            return response()->json(['created' => true]);
    }

    public function updateCategory(Request $request, int $id)
    {
        $newCategory = json_decode($request->data, true);
        $category = Categories::find($id);
        if ($category) {
            if ($category->update($newCategory)) {
                return response()->json(['updated' => true]);
            }
            abort(500, "The category can't be updated.");
        }
        abort(404, 'Category Not Found');
    }

    public function deleteCategory(int $id)
    {
        $category = Categories::find($id);
        if ($category) {
            $posts = $category->hasManyPosts()->get();
            foreach ($posts as $post) {
                $post->id = -1;
                $post->save();
            }
            $category->delete();
        }
        abort(404, 'Category Not Found');
    }
}
