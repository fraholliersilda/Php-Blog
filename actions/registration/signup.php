<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
     
    $errors = [];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email=?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();

  
    if ($user) {
        if ($user['username'] == $username) {
            $errors [] = "Username already exists.";
        } elseif ($user['email'] == $email) {
            $errors [] = "Email already exists.";
        }
    } 

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, 'user'])) {
            header("Location: ../../pages/registration/index.php");
            exit();
        } else {
            $errors[] = "Error creating account.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['messages']['errors'] = $errors;
        header("Location: ../../pages/registration/signup.php"); 
        exit();
    }
}
