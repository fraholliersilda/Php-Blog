<?php
session_start();
define('BASE_PATH', __DIR__);

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$path = str_replace('/ATIS', '', $request); // Remove base path for cleaner routing

// Include the controller
require_once BASE_PATH . '/controllers/ProfileController.php';

require_once BASE_PATH . '/db.php';
// Instantiate the ProfileController
$profileController = new App\controllers\ProfileController($conn);

// Define your route map for GET and POST requests
$routes = [
    'GET' => [
        '/views/admin/login' => 'views/admin/admin_login.php',
        '/views/admin/admins' => 'views/admin/admins.php',
        '/views/admin/users' => 'views/admin/users.php',
        '/views/posts/blog' => 'views/posts/blog_posts.php',
        '/views/posts/new' => 'views/posts/new_post.php',
        '/views/profile/edit' => function() use ($profileController) { $profileController->editProfile(); },
        '/views/profile/profile' => function() use ($profileController) { $profileController->viewProfile(); },
        '/views/registration/login' => 'views/registration/login.php',
        '/views/registration/signup' => 'views/registration/signup.php',
    ],
    'POST' => [
        '/views/registration/login' => 'views/registration/login_submit.php',
        '/views/posts/new' => 'actions/posts/new_post.php',
        '/views/posts/edit' => 'actions/posts/edit_post.php',
    ],
];

// Route for dynamic post pages (view post)
if ($method === 'GET' && strpos($path, '/views/posts/post/') === 0) {
    $_GET['id'] = intval(str_replace('/views/posts/post/', '', $path));
    require BASE_PATH . '/views/posts/post.php';
    exit;
}

// Route for dynamic post edit pages (edit post)
if ($method === 'GET' && strpos($path, '/views/posts/edit/') === 0) {
    if (isAuthenticated()) {
        $_GET['id'] = intval(str_replace('/views/posts/edit/', '', $path));
        require BASE_PATH . '/views/posts/edit_post.php';
        exit;
    } else {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

// Process static routes (GET and POST)
if (isset($routes[$method][$path])) {
    // Apply authentication checks for specific routes that need protection
    if (in_array($path, ['/views/admin/admins', '/views/posts/new'])) {
        if (!isAuthenticated()) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    // If the route is defined as a function (e.g., profile routes)
    if (is_callable($routes[$method][$path])) {
        $routes[$method][$path]();
    } else {
        // Otherwise, just require the file
        require BASE_PATH . '/' . $routes[$method][$path];
    }
} else {
    http_response_code(404);
    echo 'Page not found';
}
