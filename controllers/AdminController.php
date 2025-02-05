<?php
namespace Controllers;
error_reporting(E_ALL);
ini_set('display_errors', 1);


use PDOException;
use Requests\RegistrationRequest;
use Requests\UpdateUsernameRequest;
use Exceptions\ValidationException;
use Models\User;
use Models\Roles;
use Database;

require_once 'redirect.php';
require_once 'errorHandler.php';

class AdminController extends BaseController
{
    protected $roleModel;
    protected $userModel;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->roleModel = new Roles();
        $this->userModel = new User();
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) {
            redirect("/ATIS/views/profile/profile");
        }
    }


    public function fetchUsersByRole($role)
    {
        $roleId = $this->roleModel->findBy('role', $role)['id'] ?? null;

        if ($roleId) {
            return $this->userModel->findByRole($roleId);
        }

        return [];
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
        $this->userModel->update($id, $data);

        return null;
    }

    private function deleteUser()
    {
        $id = intval($_POST['id']);

        try {
            Database::getConnection()->beginTransaction();

            $this->userModel->delete($id);

            Database::getConnection()->commit();
        } catch (PDOException $e) {
            if (Database::getConnection()->inTransaction()) {
                Database::getConnection()->rollBack();
            }
            setErrors(["Database error: " . $e->getMessage()]);
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
                $_SESSION['role'] = 1;
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
        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] === 1) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

}


