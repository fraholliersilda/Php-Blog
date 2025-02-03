<?php
namespace Controllers;

use PDO;
use PDOException;
use Requests\RegistrationRequest;
use Requests\UpdateUsernameRequest;
use Exceptions\ValidationException;

require_once 'redirect.php';
require_once 'errorHandler.php';

class AdminController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            redirect("/ATIS/views/profile/profile");
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
                $errors = $this->updateUser();
                if ($errors) {
                    setErrors([$errors]);
                }
            } elseif ($action === 'delete') {
                $this->deleteUser();
            }
            redirect("/ATIS/views/admin/users");
        } else {
            redirect("/ATIS/views/admin/users");
        }
    }

    private function updateUser()
    {
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email'])
        ];

        try {
            UpdateUsernameRequest::validate($data);
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/admin/users");
        }

        $id = intval($_POST['id']);
        $username = $data['username'];
        $email = $data['email'];

        $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);

        return null;
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
            setErrors(["Database error" . $e->getMessage()]);
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password'])
            ];

            try {
                RegistrationRequest::validateLogin($data);
            } catch (ValidationException $e) {
                setErrors([$e->getMessage()]);
                redirect("/ATIS/views/admin/login");
            }

            $admin = $this->authenticateAdmin($data['email'], $data['password']);

            if ($admin) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role'] = 'admin';
                redirect("/ATIS/views/profile/profile");
            } else {
                setErrors(["Invalid email or password"]);
                redirect("/ATIS/views/admin/login");
            }
        } else {
            redirect("/ATIS/views/registration/login");
        }
    }

    public function showAdminLogin()
    {
        include BASE_PATH . '/views/admin/admin_login.php';
        exit();
    }

    private function authenticateAdmin($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT u.id, u.password, r.role FROM users u INNER JOIN roles r ON u.role = r.id WHERE u.email = :email AND r.role = 'admin'");
        $stmt->execute(['email' => $email]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return null;
    }
}
