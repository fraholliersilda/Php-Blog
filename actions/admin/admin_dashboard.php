<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../pages/profile/profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'update_user') {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Update user's username and email
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);

    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("DELETE FROM media WHERE user_id = ?");
            $stmt->execute([$id]);

            // Delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $conn->commit();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['messages']['errors'][] = "Database error: " . $e->getMessage();
        }
    } elseif ($action === 'create_user') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 2)");
        $stmt->execute([$username, $email, $hashedPassword]);
    }

    header("Location: ../../pages/admin/users.php");
    exit();
} else {
    header("Location: ../../pages/admin/users.php");
    exit();
}
