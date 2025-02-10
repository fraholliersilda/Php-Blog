<?php
namespace Middlewares;

use core\Middleware;
use QueryBuilder\QueryBuilder;
require_once 'redirect.php';

class PostOwnershipMiddleware implements Middleware {
    public function handle() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            die("Unauthorized access.");
        }

        // If the user is an admin, they can access any post
        if ($_SESSION['role'] == 1) {
            return; // Admins can bypass the ownership check
        }

        $url = $_SERVER['REQUEST_URI'];
        preg_match('/edit\/(\d+)/', $url, $matches);
        $postId = $matches[1] ?? null; 
    
        if (!$postId) {
            http_response_code(400);
            die("Post ID not provided.");
        }
    
        $queryBuilder = new QueryBuilder();
        $post = $queryBuilder->table('posts')
            ->select(['user_id']) // Assuming this is the column that links the post to the user
            ->where('id', '=', $postId)
            ->getOne();
    
        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            http_response_code(403);
            redirect("/ATIS/views/posts/blog");
        }
    }
}
