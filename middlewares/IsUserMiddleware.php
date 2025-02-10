<?php 
namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';

class IsUserMiddleware implements Middleware{
    public function handle(){
        // session_start();
        if($_SESSION['role'] !== 2){
            redirect("/ATIS/views/posts/blog");
            exit();
        }
    }
}