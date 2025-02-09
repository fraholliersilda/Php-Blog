<?php
namespace Controllers;

use QueryBuilder\QueryBuilder;
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
            return (new QueryBuilder())
                ->table('users u')
                ->select(['u.*', 'r.role'])
                ->join('roles r', 'u.role', '=', 'r.id')
                ->where('u.id', '=', $_SESSION['user_id'])
                ->limit(1)
                ->get()[0] ?? null;
        }
        return null;
    }
    
    // public function isAdmin()
    // {
    //     $user = $this->getLoggedInUser();

    //     // if($user->notAdmin()) {
    //     //     redirect back
    //     // }
    //     return $user && $user['role'] === 'admin';
    // }

}
