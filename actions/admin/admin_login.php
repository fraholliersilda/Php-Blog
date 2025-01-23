<?php
session_start();

require_once '../db.php';
require_once 'authentication_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $admin = authenticateAdmin($email, $password);

    if ($admin) {

        $role_id = $admin['role'];

        $stmt = $conn->prepare("SELECT role FROM roles WHERE id = :role_id");
        $stmt->execute(['role_id' => $role_id]);
        $role = $stmt->fetchColumn();

        if ($role === 'admin') {
            // session variables
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['role'] = 'admin';

            header("Location: ../../views/profile/profile");
            exit();
        }

    } else {
        $_SESSION['messages']['errors'][] = "NOT admin email or password!";
        header("Location: ../../views/admin/login");
        exit();
    }
} else {
    header("Location: ../../views/registration/login");
    exit();
}
?>