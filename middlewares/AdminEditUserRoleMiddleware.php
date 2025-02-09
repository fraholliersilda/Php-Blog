<?php
namespace Middlewares;

use core\Middleware;
use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class AdminEditUserRoleMiddleware implements Middleware {
    public function handle() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) { 
            http_response_code(403);
            die("Unauthorized access.");
        }

        if (isset($_POST['action']) && $_POST['action'] === 'update_user') {
            $userId = $_POST['id'] ?? null;

            if (!$userId) {
                http_response_code(400);
                die("User ID not provided.");
            }

            $queryBuilder = new QueryBuilder();
            $user = $queryBuilder->table('users')
                ->select(['role'])
                ->where('id', '=', $userId)
                ->getOne();

            if (!$user || $user['role'] == 1) { 
                http_response_code(403);
                redirect('/ATIS/views/admin/admins');
            }
        }
    }
}
