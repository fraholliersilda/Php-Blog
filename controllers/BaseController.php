<?php
namespace App\Controllers;

use PDO;
use Exception;

class BaseController
{
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Check if the user is logged in
    protected function checkLoggedIn()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /ATIS/views/registration/login");
            exit();
        }
    }

    // Get the logged-in user
    protected function getLoggedInUser()
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

    // Check if the user is an admin
    protected function isAdmin()
    {
        $user = $this->getLoggedInUser();
        return $user && $user['role'] === 'admin';
    }
}
