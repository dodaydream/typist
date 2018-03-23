<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function createUser(Request $request)
    {
		$user = $request->all();
        if ($user) {
            if (!isset($user['name']))
                abort(400, "Username cannot be null");
            if (!isset($user['password']))
                abort(400, "Password cannot be null");
			if (!isset($user['email']))
				abort(400, "Email cannot be null");

			if (User::where('name', $user['name'])->count())
				abort(409, "Username exists");

            $user['password'] = Hash::make($user['password']);
            $user = User::create($user);
			$resp = [
				"id" => $user['id'],
				"name" => $user['name']
			];
			return response()->json($resp, 201);
        }

        abort(400, "Empty request body");
    }

	public function retriveUser(int $id)
	{
		$user = User::find($id);
		if ($user) {
			return response()->json($user);
		}
		abort(404, "User not found");
	}

    public function updateUser(Request $request, int $id)
    {
        $user = User::find($id);
        if ($user) {
            $newUser = json_decode($request->data, true);
            $newUser->password = Hash::make($newUser->password);
            if ($user->update($newUser)) {
                return response()->json(['updated' => true]);
            }
            abort(500, 'User can\'t be updated');
        }
        abort(404, 'User not found');
    }

    public function deleteUser(int $id)
    {
        $user = User::find($id);
        if ($user) {
            $posts = $user->hasManyPosts()->get();
            foreach ($posts as $post) {
                $post->user_id = null;
                $post->save();
            }
            $user->delete();
            return response()->json(['deleted' => true]);
        }
        abort(404, 'User not found');
    }

    public function listAllUsers()
    {
        return response()->json(User::all());
    }

}
