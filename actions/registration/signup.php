<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
     
    $errors = [];

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['username'] == $username) {
            $errors[] = "Username already exists.";
        } elseif ($user['email'] == $email) {
            $errors[] = "Email already exists.";
        }
    } 

    if (empty($errors)) {
        // Role ID for 'user' is 2
        $role_id = 2;

        // Insert the new user with the correct role ID
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role_id])) {
            header("Location: ../../views/registration/login");
            exit();
        } else {
            $errors[] = "Error creating account.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['messages']['errors'] = $errors;
        header("Location: ../../views/registration/signup"); 
        exit();
    }
}
