<?php
namespace Controllers;

use PDO;
use Exception;
use Requests\RegistrationRequest;
use Exceptions\ValidationException;


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
                    redirect('/views/profile/profile');
                } else {
                    redirect('/views/registration/login');
                }
            } catch (ValidationException $e) {
                $_SESSION['messages']['errors'] = [$e->getMessage()];
                redirect('/views/registration/login');
            } catch (Exception $e) {
                $_SESSION['messages']['errors'][] = $e->getMessage();
                redirect('/views/registration/login');
            }
        } else {
            redirect('/views/registration/login');
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
    
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$data['username'], $data['email']]);
                $user = $stmt->fetch();
    
                $errors = [];
    
                if ($user) {
                    if ($user['username'] == $data['username']) {
                        $errors[] = "Username already exists.";
                    } elseif ($user['email'] == $data['email']) {
                        $errors[] = "Email already exists.";
                    }
                }
    
                if (empty($errors)) {
                    $password = password_hash($data['password'], PASSWORD_DEFAULT);
                    $role_id = 2; 
    
                    $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                    if ($stmt->execute([$data['username'], $data['email'], $password, $role_id])) {
                        redirect('/views/registration/login');
                    } else {
                        $errors[] = "Error creating account.";
                    }
                }
    
                if (!empty($errors)) {
                    $_SESSION['messages']['errors'] = $errors;
                    redirect('/views/registration/signup');
                }
    
            } catch (ValidationException $e) {
                $_SESSION['messages']['errors'] = [$e->getMessage()];
                redirect('/views/registration/signup');
            } catch (Exception $e) {
                $_SESSION['messages']['errors'][] = $e->getMessage();
                redirect('/views/registration/signup');
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
    
        header("Location: " . BASE_URL . "/views/registration/login");
        exit();
    }

    private function authenticateUser($email, $password)
    {
        $stmt = $this->conn->prepare("
            SELECT users.*, roles.role
            FROM users
            JOIN roles ON users.role = roles.id
            WHERE users.email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if ($user['role'] === 'admin') {
                $_SESSION['messages']['errors'][] = "You are admin!";
                return null;
            }

            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                $_SESSION['messages']['errors'][] = "Invalid email or password!";
            }
        }

        return null; 
    }
}
