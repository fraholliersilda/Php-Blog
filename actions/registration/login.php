<?php
session_start();
require_once '../db.php';
require_once 'authentication.php';
require_once '../admin/authentication_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    
    $admin = authenticateAdmin($email, $password);
    if ($admin) {
        header("Location: ../../views/registration/login");
        $_SESSION['messages']['errors'][] = "YOU ARE ADMIN! ";
        exit();
    }else{
    $user = authenticateUser($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        header("Location: ../../views/profile/profile");
        exit();
    } else {
        $_SESSION['messages']['errors'][] = "Invalid email or password!";
        header("Location: ../../views/registration/login");
        exit();
    }}
} else {
    header("Location: ../../views/registration/login");
    exit();
}
?>
