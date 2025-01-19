<?php
session_start(); 

require_once '../db.php';  
require_once 'authentication.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $admin = authenticateAdmin($email, $password);


    if ($admin) {
        // session variables
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['role'] = 'admin';  

        header("Location: ../../pages/admin_dashboard.php");
        exit();
    } else {
        $_SESSION['messages']['errors'][] = "NOT admin email or password!";
        header("Location: ../../pages/registration/admin_login.php");
        exit();
    }
} else {
    header("Location: ../../pages/registration/index.php");
    exit();
}
?>
