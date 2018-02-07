<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class TokenController extends Controller
{
    public function getToken(Request $request)
    {
        if ($request->has('name') && $request->has('password'))
            $user = User::where('name', $request->input('name'))
                        ->where('password', Hash::make($request->input('password')))->first();
            if ($user) {
                $data = [
                    'uid' => $user->id,
                    'exp' => time() + getenv('JWT_TIME_ALIVE')
                ];
                $token = JWT::encode($data, getenv('JWT_SECRET'));
            }
    }

    public function refreshToken(Request $request)
    {
        
    }

}
