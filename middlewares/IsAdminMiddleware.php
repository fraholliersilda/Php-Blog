<?php 
namespace Middlewares;

use core\Middleware;

class IsAdminMiddleware implements Middleware{
    public function handle(){
        // session_start();
        if($_SESSION['role'] !== 1){
            die("Unauthorized acces.");
        }
    }
}