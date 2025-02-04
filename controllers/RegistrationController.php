<?php

namespace Controllers;
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
use PDO;
use Exception;
use Requests\RegistrationRequest;
use Exceptions\ValidationException;
use QueryBuilder\QueryBuilder;

require_once 'redirect.php';
require_once 'errorHandler.php';

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
    
                $user = $this->authenticateUser($data['email'], $data['password']);
    
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    redirect("/ATIS/views/profile/profile");
                } else {
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
    
                $user = (new QueryBuilder())
                    ->table('users')
                    ->select(['*'])
                    ->where('username', '=', $data['username'])
                    ->where('email', '=', $data['email'])
                    ->get();
    
                $errors = [];
    
                if (!empty($user)) {
                    if ($user[0]['username'] === $data['username']) {
                        $errors[] = "Username already exists.";
                    }
                    if ($user[0]['email'] === $data['email']) {
                        $errors[] = "Email already exists.";
                    }
                }
    
                if (empty($errors)) {
                    $password = password_hash($data['password'], PASSWORD_DEFAULT);
                    $role_id = 2;
    
                    $inserted = (new QueryBuilder())
                        ->table('users')
                        ->insert([
                            'username' => $data['username'],
                            'email' => $data['email'],
                            'password' => $password,
                            'role' => $role_id
                        ]);
    
                    if ($inserted) {
                        redirect("/ATIS/views/registration/login");
                    } else {
                        setErrors(["Error creating account."]);
                    }
                }
    
                if (!empty($errors)) {
                    setErrors($errors);
                    redirect("/ATIS/views/registration/signup");
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
        redirect("/ATIS/views/registration/login");
    }

    private function authenticateUser($email, $password)
{
    $queryBuilder = new QueryBuilder();
    
    $user = $queryBuilder->table('users')
        ->select(['users.*', 'roles.role'])
        ->join('roles', 'users.role', '=', 'roles.id')
        ->where('email', '=', $email) 
        ->limit(1)
        ->get();

    if ($user) {
        $user = $user[0]; 

        if ($user['role'] === 'admin') {
            setErrors(["You are admin!"]);
            return null;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        } else {
            setErrors(["Invalid email or password!"]);
        }
    }

    return null;
    }
    
}

    