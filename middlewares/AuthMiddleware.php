<?php 

namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class AuthMiddleware implements Middleware{
    public function handle(){
        if(!isset($_SESSION['user_id'])){
            redirect('/ATIS/views/registration/login');
            exit();
        }
    }
}