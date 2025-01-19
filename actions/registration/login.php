<?php
session_start();
require_once '../db.php';
require_once 'authentication.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    
    $admin = authenticateAdmin($email, $password);
    if ($admin) {
        header("Location: ../../pages/registration/index.php");
        $_SESSION['messages']['errors'][] = "YOU ARE ADMIN! ";
        exit();
    }else{
    $user = authenticateUser($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        header("Location: ../../pages/home.php");
        exit();
    } else {
        $_SESSION['messages']['errors'][] = "Invalid email or password!";
        header("Location: ../../pages/registration/index.php");
        exit();
    }}
} else {
    header("Location: ../../pages/registration/index.php");
    exit();
}
?>
