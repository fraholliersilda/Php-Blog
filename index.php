<?php
define('BASE_PATH', __DIR__);

$request = $_SERVER['REQUEST_URI'];

$path = str_replace('/ATIS', '', $request);

// Handle routing
if (preg_match('#^/views/posts/post/(\d+)$#', $path, $matches)) {
    // Get the post ID 
    $_GET['id'] = $matches[1];
    require __DIR__ . '/views/posts/post.php';
} else if(preg_match('#^/views/posts/edit/(\d+)$#', $path, $matches)){
    $_GET['id'] = $matches[1];
    require __DIR__ . '/views/posts/edit_post.php';
}else {
    switch ($path) {
        case '/views/admin/login':
            require __DIR__ . '/views/admin/admin_login.php';
            break;

        case '/views/admin/admins':
            require __DIR__ . '/views/admin/admins.php';
            break;

        case '/views/admin/users':
            require __DIR__ . '/views/admin/users.php';
            break;

        case '/views/posts/blog':
            require __DIR__ . '/views/posts/blog_posts.php';
            break;

        case '/views/posts/new':
            require __DIR__ . '/views/posts/new_post.php';
            break;

        case '/views/profile/edit':
            require __DIR__ . '/views/profile/edit_profile.php';
            break;

        case '/views/profile/profile':
            require __DIR__ . '/views/profile/profile.php';
            break;

        case '/views/registration/login':
            require __DIR__ . '/views/registration/login.php';
            break;

        case '/views/registration/signup':
            require __DIR__ . '/views/registration/signup.php';
            break;

        default:
            http_response_code(404);
            echo 'Page not found';
            break;
    }
}
