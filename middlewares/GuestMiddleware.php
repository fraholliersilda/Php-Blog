<?php
namespace Middlewares;

use core\Middleware;
require_once 'redirect.php';
class GuestMiddleware implements Middleware {
    public function handle(){
        // session_start();
        if (isset($_SESSION['user_id'])) {
            redirect("/ATIS/views/profile/profile");
            exit();
        }
    }
}
