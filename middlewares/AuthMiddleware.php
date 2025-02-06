<?php 

namespace Middlewares;

use core\Middleware;

class AuthMiddleware implements Middleware{
    public function handle(){
        // session_start();
        if(!isset($_SESSION['user_id'])){
            header('Location: /ATIS/views/registration/login');
            exit();
        }
    }
}