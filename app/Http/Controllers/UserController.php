<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        return response()->json(User::all());
    }

    public function createUser(Request $request)
    {
        $user = json_decode($request->data);
        if ($user) {
            if (!$user->name) {
                abort(400, "Username cannot be null");
            }

            if(!$user->password) {
                abort(400, "Password cannot be null");
            }
            $user->password = Hash::make($user->password);

            User::create($user);
        }

        abort(400, "User cannot be created with null argument");
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
                $post->user_id = -1;
                $post->save();
            }
            $user->delete();
            return response()->json(['deleted' => true]);
        }
        abort(404, 'User not found');
    }

}
