<?php

namespace Controllers;
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
use PDO;
use Exception;
use Requests\RegistrationRequest;
use Exceptions\ValidationException;
use Models\User;

require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

class RegistrationController extends BaseController
{
    public function __construct(PDO $conn)
    {
        parent::__construct($conn);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
            ];
    
            try {
                RegistrationRequest::validateLogin($data);
    
                $user = (new User)->findByEmail($data['email']);
    
                if ($user) {
                    // Check if the user is an admin first
                    if ($user['role'] === 1) {
                        setErrors(["You are admin"]);
                        redirect("/ATIS/views/registration/login");
                        return; // Ensure no further processing happens for admins
                    }
    
                    if (password_verify($data['password'], $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role'];
                        redirect("/ATIS/views/profile/profile");
                    } else {
                        setErrors(["Invalid email or password!"]);
                        redirect("/ATIS/views/registration/login");
                    }
                } else {
                    setErrors(["User not found."]);
                    redirect("/ATIS/views/registration/login");
                }
            } catch (ValidationException $e) {
                setErrors([$e->getMessage()]);
                redirect("/ATIS/views/registration/login");
            } catch (Exception $e) {
                setErrors([$e->getMessage()]);
                redirect("/ATIS/views/registration/login");
            }
        } else {
            redirect("/ATIS/views/registration/login");
        }
    }
    

    public function showLogin()
    {
        include BASE_PATH . '/views/registration/login.php';
        exit();
    }

    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
            ];

            try {
                RegistrationRequest::validateSignup($data);

                $existingUser =(new User)->findByEmail($data['email']) ??(new User)->findByUsername($data['username']);

                if ($existingUser) {
                    $errors = [];

                    if ($existingUser['email'] === $data['email']) {
                        $errors[] = "Email already exists.";
                    }
                    if ($existingUser['username'] === $data['username']) {
                        $errors[] = "Username already exists.";
                    }

                    setErrors($errors);
                    redirect("/ATIS/views/registration/signup");
                }

                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $data['role'] = 2;

                if ((new User)->create($data)) {
                    setSuccessMessages(['User created!']);
                    redirect("/ATIS/views/registration/login");
                    
                } else {
                    setErrors(["Error creating account."]);
                }
            } catch (ValidationException $e) {
                setErrors([$e->getMessage()]);
                redirect("/ATIS/views/registration/signup");
            } catch (Exception $e) {
                setErrors([$e->getMessage()]);
                redirect("/ATIS/views/registration/signup");
            }
        }
    }

    public function showSignup()
    {
        include BASE_PATH . '/views/registration/signup.php';
        exit();
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        setSuccessMessages(['User logged out!']);
        redirect("/ATIS/views/registration/login");
    }

}

