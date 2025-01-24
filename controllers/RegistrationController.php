<?php
namespace App\Controllers;

use PDO;
use Exception;

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
                $_SESSION['role'] = 'user';
                redirect('/views/profile/profile'); // Use redirect function
            } else {
                $_SESSION['messages']['errors'][] = "Invalid email or password!";
                redirect('/views/registration/login'); // Use redirect function
            }
        } else {
            redirect('/views/registration/login'); // Use redirect function
        }
    }

    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $errors = [];

            // Check if the username or email already exists
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
                // Role ID for 'user' is 2
                $role_id = 2;

                // Insert the new user with the correct role ID
                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $email, $password, $role_id])) {
                    redirect('/views/registration/login'); // Use redirect function
                } else {
                    $errors[] = "Error creating account.";
                }
            }

            if (!empty($errors)) {
                $_SESSION['messages']['errors'] = $errors;
                redirect('/views/registration/signup'); // Use redirect function
            }
        }
    }

    private function authenticateUser($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }
}
