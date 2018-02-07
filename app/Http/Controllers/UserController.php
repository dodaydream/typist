<?php

namespace App\Http\Controllers;
use App\Users;
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

            User::create($user);
        }

        abort(400, "User cannot be created with null argument");
    }

    public function updateUser(Request $request, int $id)
    {
        // $user = 
    }

    public function deleteUser(int $id)
    {

    }

}
