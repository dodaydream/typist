<?php

namespace App\Console\Commands;

use App\Users;
<<<<<<< HEAD
use Illuminate\Console\Command;
=======
>>>>>>> e0e228597d20c0a3c8b68dce63b5e1ab4ca4d2fc

class Install extends Command
{
    protected $signature = 'install';

    protected $description = 'Install Typist API';

    public function handle()
    {
<<<<<<< HEAD
        $email = $this->ask('email');
=======
        $email = $this->ask('email')
>>>>>>> e0e228597d20c0a3c8b68dce63b5e1ab4ca4d2fc
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
