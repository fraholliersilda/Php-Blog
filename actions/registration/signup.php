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

    // If there are no errors, proceed to insert the new user
    if (empty($errors)) {
        // Role ID for 'user' is 2, as per your explanation
        $role_id = 2;

        // Insert the new user with the correct role ID
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role_id])) {
            header("Location: ../../pages/registration/index.php");
            exit();
        } else {
            $errors[] = "Error creating account.";
        }
    }

    // If there are errors, redirect back to the signup page with error messages
    if (!empty($errors)) {
        $_SESSION['messages']['errors'] = $errors;
        header("Location: ../../pages/registration/signup.php"); 
        exit();
    }
}
