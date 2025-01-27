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

    public function fetchUsersByRole($role)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email FROM users WHERE role = (SELECT id FROM roles WHERE role = :role)");
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAdmins()
    {
        $this->checkAdmin();
        $admins = $this->fetchUsersByRole('admin');
        require BASE_PATH . '/views/admin/admins.php';
    }

    public function listUsers()
    {
        $this->checkAdmin();
        $users = $this->fetchUsersByRole('user');
        require BASE_PATH . '/views/admin/users.php';
    }


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

    private function updateUser()
    {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Update user's username and email
        $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);
    }

    private function deleteUser()
    {
        $id = intval($_POST['id']);
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("DELETE FROM media WHERE user_id = ?");
            $stmt->execute([$id]);

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

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);


            $admin = $this->authenticateAdmin($email, $password);

            if ($admin) {
                $role = $admin['role'];

                if ($role === 'admin') {
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['role'] = 'admin';

                    header("Location: /ATIS/views/profile/profile");
                    exit();
                }
            } else {
                $_SESSION['messages']['errors'][] = "Invalid email or password!";
                header("Location: /ATIS/views/admin/login");
                exit();
            }
        } else {
            header("Location: /ATIS/views/registration/login");
            exit();
        }
    }
    private function authenticateAdmin($email, $password)
    {
        $stmt = $this->conn->prepare("
            SELECT u.id, u.password, r.role FROM users u
            INNER JOIN roles r ON u.role = r.id
            WHERE u.email = :email AND r.role = 'admin'
        ");
        $stmt->execute(['email' => $email]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return null;
    }


}
