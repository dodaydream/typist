<?php

namespace App\Providers;

use App\Users;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use \Firebase\JWT\JWT;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $key = substr($request->header('Authorization'), 4);
                try {
                    $user = JWT::decode($key, getenv('JWT_SECRET'), ['HS256']);
                } catch (\Firebase\JWT\ExpiredException $e) {
                    abort(401, 'Invalid Credential');
                }
                if (!empty($user) && isset($user->uid)) {
                    $request->request->add(['uid' => $user->uid]);
                    return $user->uid;
                }
            }
            abort(401, 'Unauthorized');
        });
    }
}
