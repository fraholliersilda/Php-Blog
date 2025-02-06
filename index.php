<?php

require_once __DIR__ . '/vendor/autoload.php';
session_start();
define('BASE_PATH', __DIR__);
define('BASE_URL', '/ATIS');


$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];


$path = str_replace(BASE_URL, '', $request);
require_once 'redirect.php';

use core\MiddlewareHandler;
use Middlewares\AuthMiddleware;
use Middlewares\IsAdminMiddleware;
use Exceptions\ValidationException;
use Controllers\ProfileController;
use Controllers\RegistrationController;
use Controllers\PostsController;
use Controllers\AdminController;

require_once BASE_PATH . '/Database.php';


$profileController = new ProfileController($conn);
$registrationController = new RegistrationController($conn);
$postsController = new PostsController($conn);
$adminController = new AdminController($conn);


$routes = [
    'GET' => [
        '/logout' =>  
        [
            fn() => $registrationController->logout(),
            [AuthMiddleware::class]
        ]
        ,
        '/views/admin/login' =>[fn() => $adminController->showAdminLogin(),[]], 
        '/views/posts/new' => 
        [
            fn() => $postsController->showNewPost(), 
            [AuthMiddleware::class]
        ],
        '/views/posts/blog' => [fn() => $postsController->listPosts(),[]],
        '/views/posts/post/{id}' => [fn($id) => $postsController->viewPost($id),[]],
        '/views/posts/edit/{id}' =>
        [
            fn($id) => $postsController->editPost($id), 
            [AuthMiddleware::class]
        ],
        '/views/admin/admins' =>
        [
            fn() => $adminController->listAdmins(),
            [AuthMiddleware::class, IsAdminMiddleware::class]
        ],
        '/views/admin/users' => 
        [
            fn() => $adminController->listUsers(), 
            [AuthMiddleware::class, IsAdminMiddleware::class]
        ],
        '/views/profile/edit' => 
        [
            fn() => $profileController->editProfile(),
            [AuthMiddleware::class]
        ],
        '/views/profile/profile' => 
        [
            fn() => $profileController->viewProfile(),
            [AuthMiddleware::class]
        ],
        '/views/registration/login' => [fn() => $registrationController->showLogin(),[]],
        '/views/registration/signup' => [fn() => $registrationController->showSignup(),[]],
    ],
    'POST' => [
        '/views/registration/login' => [fn() => $registrationController->login(),[]],
        '/views/admin/login' => [fn() => $adminController->login(),[]],
        '/views/registration/signup' => [fn() => $registrationController->signup(),[]],
        '/views/posts/new' => 
        [
            fn() => $postsController->createPost(), 
            [AuthMiddleware::class]
        ],
        '/views/posts/edit/{id}' =>  
        [
            fn() => $postsController->editPost($_POST['id']),
            [AuthMiddleware::class]
        ],
        '/posts/delete/{id}' => 
        [
            fn($id) => $postsController->deletePost($id),
            [AuthMiddleware::class, IsAdminMiddleware::class]
        ],
        '/views/admin/users' => 
        [
            fn() => $adminController->handleUserActions(),
            [AuthMiddleware::class, IsAdminMiddleware::class]
        ],
        '/views/profile/edit' => 
        [
            fn() => $profileController -> updateProfile($_POST, $_FILES),
            [AuthMiddleware::class]
        ],
    ],
];



$routeFound = false;
foreach ($routes[$method] as $route => $action) {
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_-]+)', $route);
    if (preg_match("#^$pattern$#", $path, $matches)) {
        array_shift($matches); 

        try {
            $callback = $action[0]; 
            $middlewares = $action[1] ?? [];

            // Check middlewares
            MiddlewareHandler::run($middlewares);

            if (is_callable($callback)) {
                $callback(...$matches);
            } else {
                require BASE_PATH . '/' . $callback;
            }

            $routeFound = true;
        }
        catch (ValidationException $exception) {
            setErrors([$exception->getMessage()]);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (Exception $exception) {
            setErrors(['Something went wrong. Please try again later.']);
            redirect("/ATIS/views/500.php");
        }
        exit;
    }
}


// Route not found - Redirect to 404 page
if (!$routeFound) {
    require BASE_PATH . '/views/404page.php';
    exit;
}