<?php
session_start();
define('BASE_PATH', __DIR__);
define('BASE_URL', '/ATIS');

// user authenticated?
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

//handle redirects
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit();
}


$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];


$path = str_replace(BASE_URL, '', $request);


require_once BASE_PATH . '/controllers/ProfileController.php';
require_once BASE_PATH . '/controllers/RegistrationController.php';
require_once BASE_PATH . '/controllers/PostsController.php';
require_once BASE_PATH . '/controllers/AdminController.php';
require_once BASE_PATH . '/db.php';

$profileController = new App\Controllers\ProfileController($conn);
$registrationController = new App\Controllers\RegistrationController($conn);
$postsController = new App\Controllers\PostsController($conn);
$adminController = new App\Controllers\AdminController($conn);


$routes = [
    'GET' => [
        '/logout' => fn() => $registrationController->logout(),
        '/views/admin/login' => fn() => $adminController->showAdminLogin(),
        '/views/posts/new' => fn() => $postsController->showNewPost(),
        '/views/posts/blog' => fn() => $postsController->listPosts(),
        '/views/posts/post/{id}' => fn($id) => $postsController->viewPost($id),
        '/views/posts/edit/{id}' =>fn($id) => $postsController->editPost($id),
        '/views/admin/admins' => fn() => $adminController->listAdmins(),
        '/views/admin/users' => fn() => $adminController->listUsers(),
        '/views/profile/edit' => fn() => $profileController->editProfile(),
        '/views/profile/profile' => fn() => $profileController->viewProfile(),
        '/views/registration/login' => fn() => $registrationController->showLogin(),
        '/views/registration/signup' => fn() => $registrationController->showSignup(),
    ],
    'POST' => [
        '/views/registration/login' => fn() => $registrationController->login(),
        '/views/admin/login' => fn() => $adminController->login(),
        '/views/registration/signup' => fn() => $registrationController->signup(),
       '/views/posts/new' => fn() => $postsController->createPost(),
        '/views/posts/edit/{id}' =>  fn() => $postsController->editPost($_POST['id']),
        '/posts/delete/{id}' => fn($id) => $postsController->deletePost($id),
        '/views/admin/users' => fn() => $adminController->handleUserActions(),
        '/views/profile/edit' => fn() => $profileController -> updateProfile($_POST, $_FILES)
    ],
];



$routeFound = false;
foreach ($routes[$method] as $route => $action) {
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    if (preg_match("#^$pattern$#", $path, $matches)) {
        array_shift($matches); 
        try {
            if (is_callable($action)) {
                $action(...$matches);
            } else {
                require BASE_PATH . '/' . $action;
            }
            $routeFound = true;
        }
        catch(ValidationException $exception) {
            // return back
        }
         catch(Exception $exception) {
            // throw 500
        }
        exit;
    }
}

// Route not found - Redirect to 404 page
if (!$routeFound) {
    require BASE_PATH . '/views/404page.php';
    exit;
}