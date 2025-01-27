<?php
namespace App\Controllers;

use PDO;
use PDOException;

class AdminController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /ATIS/views/profile/profile");
            exit();
        }
    }

    // Fetch users by role
    public function fetchUsersByRole($role)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email FROM users WHERE role = (SELECT id FROM roles WHERE role = :role)");
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAdmins()
{
    $this->checkAdmin();
    $admins = $this->fetchUsersByRole('admin'); // Fetch admins by role
    require BASE_PATH . '/views/admin/admins.php'; // Pass admins to the view
}

public function listUsers()
{
    $this->checkAdmin();
    $users = $this->fetchUsersByRole('user'); 
    require BASE_PATH . '/views/admin/users.php'; 
}


    // Handle user actions: update, delete
    public function handleUserActions()
    {
        $this->checkLoggedIn();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];

            if ($action === 'update_user') {
                $this->updateUser();
            } elseif ($action === 'delete') {
                $this->deleteUser();
            }
            header("Location: /ATIS/views/admin/users");
            exit();
        } else {
            header("Location: /ATIS/views/admin/users");
            exit();
        }
    }

    // Update user information
    private function updateUser()
    {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Update user's username and email
        $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);
    }

    // Delete user and their media
    private function deleteUser()
    {
        $id = intval($_POST['id']);
        try {
            $this->conn->beginTransaction();

            // Delete media related to the user
            $stmt = $this->conn->prepare("DELETE FROM media WHERE user_id = ?");
            $stmt->execute([$id]);

            // Delete user
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $this->conn->commit();
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $_SESSION['messages']['errors'][] = "Database error: " . $e->getMessage();
        }
    }

    // Admin login
// Ensure session is started at the beginning of the file


public function login()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Debugging step: Log login attempt
        error_log("Login attempt: email = $email");

        $admin = $this->authenticateAdmin($email, $password);

        if ($admin) {
            $role = $admin['role'];

            if ($role === 'admin') {
                // Set session variables for the logged-in user
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role'] = 'admin';

                // Redirect to admin profile page
                header("Location: /ATIS/views/profile/profile");
                exit();
            }
        } else {
            // If authentication fails, store the error message in the session
            $_SESSION['messages']['errors'][] = "Invalid email or password!";
            // Redirect back to the login page
            header("Location: /ATIS/views/admin/login");
            exit();
        }
    } else {
        // Redirect to login page if method is not POST
        header("Location: /ATIS/views/registration/login");
        exit();
    }
}


    // Authenticate admin credentials

    private function authenticateAdmin($email, $password)
    {
        // Prepare the query, fetching user id, password, and role
        $stmt = $this->conn->prepare("
            SELECT u.id, u.password, r.role FROM users u
            INNER JOIN roles r ON u.role = r.id
            WHERE u.email = :email AND r.role = 'admin'
        ");
        $stmt->execute(['email' => $email]);
    
        // Fetch the admin user
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if the admin exists and the password is correct
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;  // Return the admin data if authentication is successful
        }
    
        return null;  // Return null if no match is found or password is incorrect
    }
    
    
}
