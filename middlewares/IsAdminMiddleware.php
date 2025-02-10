<?php 
namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class IsAdminMiddleware implements Middleware{
    public function handle(){
        // session_start();
        if($_SESSION['role'] !== 1){
            redirect("/ATIS/views/profile/profile");
            exit();
        }
    }
}