<?php
namespace App\Controllers;

use PDO;

class BaseController
{
   public $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function checkLoggedIn()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /ATIS/views/registration/login");
            exit();
        }
    }

    public function getLoggedInUser()
    {
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->conn->prepare("SELECT u.*, r.role FROM users u
                                          INNER JOIN roles r ON u.role = r.id
                                          WHERE u.id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function isAdmin()
    {
        $user = $this->getLoggedInUser();
        return $user && $user['role'] === 'admin';
    }
}
