<?php

namespace App\Http\Controllers;
use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use \Firebase\JWT\JWT;

class TokenController extends Controller
{
    public function createToken(Request $request)
    {
        $req = $request->all();
        if (!isset($req['password']))
            abort(400, "Password cannot be null");

        if (isset($req['name']))
            $user = Users::where('name', $req['name']);
        else if (isset($req['email']))
            $user = Users::where('email', $req['email']);
        else
            abort(400, "Username / Email cannot be null");

        $user = $user->first();

        if (isset($user) && Hash::check( (string) $req['password'], $user->password)) {
            $payload = [
                'uid' => $user->id,
                'exp' => time() + getenv('JWT_TIME_ALIVE')
            ];
            $token = JWT::encode($payload, getenv('JWT_SECRET'));
            return response()->json(['token' => $token]);
        }
        abort(401, "Invalid credential");
    }

    public function refreshToken(Request $request)
    {
        $header = $request->header('Authorization');
        $key = substr($header, 4);
        $user = JWT::decode($key, getenv('JWT_SECRET'), ['HS256']);
        $user->exp = $user->exp + getenv('JWT_TIME_ALIVE');
        $token = JWT::encode($user, getenv('JWT_SECRET'));
        return response()->json(['token' => $token]);
    }

}
