<?php
session_start();
define('BASE_PATH', __DIR__);
define('BASE_URL', '/ATIS');

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

// Remove base URL for cleaner routing
$path = str_replace(BASE_URL, '', $request);

// Include necessary files
require_once BASE_PATH . '/controllers/ProfileController.php';
require_once BASE_PATH . '/controllers/RegistrationController.php';
require_once BASE_PATH . '/controllers/PostsController.php';
require_once BASE_PATH . '/controllers/AdminController.php';
require_once BASE_PATH . '/db.php';

// Instantiate controllers
$profileController = new App\Controllers\ProfileController($conn);
$registrationController = new App\Controllers\RegistrationController($conn);
$postsController = new App\Controllers\PostsController($conn);
$adminController = new App\Controllers\AdminController($conn);

// Define route map
$routes = [
    'GET' => [
        '/logout' => fn() => $registrationController->logout(),
        '/views/admin/login' => 'views/admin/admin_login.php',
        '/views/posts/new' => 'views/posts/new_post.php',
        '/views/posts/blog' => fn() => $postsController->listPosts(),
        '/views/posts/post/{id}' => fn($id) => $postsController->viewPost($id),
        '/views/posts/edit/{id}' =>fn($id) => $postsController->editPost($id),
        '/views/admin/admins' => fn() => $adminController->listAdmins(),
        '/views/admin/users' => fn() => $adminController->listUsers(),
        '/views/profile/edit' => fn() => $profileController->editProfile(),
        '/views/profile/profile' => fn() => $profileController->viewProfile(),
        '/views/registration/login' => 'views/registration/login.php',
        '/views/registration/signup' => 'views/registration/signup.php',
    ],
    'POST' => [
        '/views/registration/login' => fn() => $registrationController->login(),
        '/views/admin/login' => fn() => $adminController->login(),
        '/views/registration/signup' => fn() => $registrationController->signup(),
        '/views/posts/new' => fn() => $postsController->createPost($_POST['title'], $_POST['description'], $_FILES['cover_photo']),
        '/views/posts/edit/{id}' =>  fn() => $postsController->editPost($_POST['id']),
        '/posts/delete/{id}' => fn($id) => $postsController->deletePost($id),
        '/views/admin/users' => fn() => $adminController->handleUserActions(),
        '/views/profile/edit' => fn() => handleProfileActions($profileController),
    ],
];

// Handle profile actions
function handleProfileActions($controller) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'updateUsername':
                $controller->updateUsername($_POST);
                break;
            case 'updatePassword':
                $controller->updatePassword($_POST);
                break;
            case 'updateProfilePicture':
                $controller->updateProfilePicture($_POST, $_FILES);
                break;
            default:
                http_response_code(400); // Bad Request
                echo 'Invalid action';
        }
    } else {
        http_response_code(400); // Bad Request
        echo 'No action specified';
    }
}

// Process request
foreach ($routes[$method] as $route => $action) {
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    if (preg_match("#^$pattern$#", $path, $matches)) {
        array_shift($matches); // Remove the full match
        if (is_callable($action)) {
            $action(...$matches);
        } else {
            require BASE_PATH . '/' . $action;
        }
        exit;
    }
}

// Route not found
http_response_code(404);
echo 'Page not found';
