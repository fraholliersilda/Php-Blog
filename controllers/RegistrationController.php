<?php
namespace App\Controllers;

use PDO;

class RegistrationController
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
    
            $user = $this->authenticateUser($email, $password);
    

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role']; 
                redirect('/views/profile/profile'); 
            } else {
                if (!isset($_SESSION['messages']['errors'])) {
                    $_SESSION['messages']['errors'][] = "Invalid email or password!";
                }
                redirect('/views/registration/login'); 
            }
        } else {
            redirect('/views/registration/login'); 
        }
    }
    
    

    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $errors = [];

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $user = $stmt->fetch();

            if ($user) {
                if ($user['username'] == $username) {
                    $errors[] = "Username already exists.";
                } elseif ($user['email'] == $email) {
                    $errors[] = "Email already exists.";
                }
            }

            if (empty($errors)) {
                $role_id = 2;//for users

                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $email, $password, $role_id])) {
                    redirect('/views/registration/login'); 
                } else {
                    $errors[] = "Error creating account.";
                }
            }

            if (!empty($errors)) {
                $_SESSION['messages']['errors'] = $errors;
                redirect('/views/registration/signup'); 
            }
        }
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
