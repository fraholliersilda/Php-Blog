<?php
session_start();
define('BASE_PATH', __DIR__);
define('BASE_URL', '/ATIS'); // Define the base URL

// Helper function to check if the user is authenticated
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Helper function to handle redirects
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit();
}

// Parse request
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove base path for cleaner routing
$path = str_replace(BASE_URL, '', $request);

// Include necessary files
require_once BASE_PATH . '/controllers/ProfileController.php';
require_once BASE_PATH . '/controllers/RegistrationController.php';
require_once BASE_PATH . '/db.php';

// Instantiate controllers
$profileController = new App\Controllers\ProfileController($conn);
$registrationController = new App\Controllers\RegistrationController($conn);

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
        '/views/registration/login' => function() use ($registrationController) { $registrationController->login(); },
        '/views/registration/signup' => function() use ($registrationController) { $registrationController->signup(); },
        '/views/posts/new' => 'actions/posts/new_post.php',
        '/views/posts/edit' => 'actions/posts/edit_post.php',
        '/views/profile/edit' => function() use ($profileController) {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'updateUsername':
                        $profileController->updateUsername($_POST);
                        break;
                    case 'updatePassword':
                        $profileController->updatePassword($_POST);
                        break;
                    case 'updateProfilePicture':
                        $profileController->updateProfilePicture($_POST, $_FILES);
                        break;
                    default:
                        http_response_code(400); // Bad Request
                        echo 'Invalid action';
                        break;
                }
            } else {
                http_response_code(400); // Bad Request
                echo 'No action specified';
            }
        },
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

    // If the route is defined as a function (e.g., profile routes or registration actions)
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
