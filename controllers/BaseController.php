<?php
namespace Controllers;

use PDO;

require_once 'redirect.php';

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
            redirect("/ATIS/views/registration/login");
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
