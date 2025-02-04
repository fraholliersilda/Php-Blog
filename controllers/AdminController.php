<?php
namespace Controllers;
error_reporting(E_ALL);
ini_set('display_errors', 1);


use PDOException;
use Requests\RegistrationRequest;
use Requests\UpdateUsernameRequest;
use Exceptions\ValidationException;
use QueryBuilder\QueryBuilder;
use Database;

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
        $queryBuilder = new QueryBuilder();
        
        $roleId = (new QueryBuilder())
            ->table('roles')
            ->select(['id'])
            ->where('role', '=', $role)
            ->get();
    
        if (!empty($roleId)) {
            return $queryBuilder
                ->table('users')
                ->select(['id', 'username', 'email'])
                ->where('role', '=', $roleId[0]['id'])
                ->get();
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
    
        $queryBuilder = new QueryBuilder();
        $queryBuilder->table('users')
            ->update($data)
            ->where('id', '=', $id)
            ->execute();
    
        return null;
    }
    

    private function deleteUser()
    {
        $id = intval($_POST['id']);
    
        try {
            Database::getConnection()->beginTransaction();
    
            (new QueryBuilder())
                ->table('media')
                ->where('user_id', '=', $id)
                ->delete()
                ->execute();
    
            (new QueryBuilder())
                ->table('users')
                ->where('id', '=', $id)
                ->delete()
                ->execute();
    
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
        $queryBuilder = new QueryBuilder();
        
        $admin = $queryBuilder->table('users u')
            ->select(['u.id', 'u.password', 'r.role'])
            ->join('roles r', 'u.role', '=', 'r.id')
            ->where('u.email', '=', $email)  
            ->where('r.role', '=', 'admin')  
            ->get();
        
        if ($admin) {
            $admin = $admin[0];

            if (password_verify($password, $admin['password'])) {
                return $admin;
            }
        }
    
        return null;
    }

}


