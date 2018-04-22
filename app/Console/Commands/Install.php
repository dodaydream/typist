<?php

namespace App\Console\Commands;

use App\Users;

class Install extends Command
{
    protected $signature = 'install';

    protected $description = 'Install Typist API';

    public function handle()
    {
        $email = $this->ask('email')
        $username = $this->ask('Username');
        $password = $this->secret('Password');

        $user = [
            'name' => $username,
            'password' => Hash::make($password),
            'email' => $email
        ];

        Users::create($user);
        
        echo 'completed';
    }
}
