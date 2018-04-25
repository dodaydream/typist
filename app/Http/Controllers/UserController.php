<?php

namespace App\Http\Controllers;
use App\Users;
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

            if (Users::where('name', $user['name'])->count())
                abort(409, "Username exists");
            if (Users::where('email', $user['email'])->count())
                abort(409, "Email already registered");

            $user = Users::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'description' => isset($user['description']) ? $user['description'] : null
            ]);

            return response()->json([
                "id" => $user['id'],
                "name" => $user['name']
            ], 201);
        }

        abort(400, "Empty request body");
    }

    public function retriveUser(int $id)
    {
        $user = Users::find($id);
        if ($user) {
            return response()->json($user);
        }
        abort(404, "User not found");
    }

    public function updateUser(Request $request, int $id)
    {
        $user = Users::find($id);
        if ($user) {
            $newUser = $request->all();
            if (isset($newUser['password']))
                $newUser['password'] = Hash::make($newUser['password']);
            if ($user->update($newUser)) {
                $resp = [
                    "id" => $user['id'],
                    "name" => $user['name'],
                    "email" => $user['email'],
                    "password_updated" => false
                ];

                if (isset($newUser['password']))
                    $resp['password_updated'] = true;
                return response()->json($resp);
            }
            abort(500, 'User can\'t be updated');
        }
        abort(404, 'User not found');
    }

    public function deleteUser(int $id)
    {
        $user = Users::find($id);
        if ($user) {
            $posts = $user->hasManyPosts()->get();
            foreach ($posts as $post) {
                $post['user_id'] = null;
                $post->save();
            }
            $user->delete();
            return response()->json(['deleted' => true]);
        }
        abort(404, 'User not found');
    }

    public function listAllUsers()
    {
        return response()->json(Users::all());
    }

}
