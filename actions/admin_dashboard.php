<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/registration/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = intval($_POST['id']);

    if ($action === 'update') {
        $email = trim($_POST['email']);
        $role = $_POST['role'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['messages']['errors'][] = "Invalid email format.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
            $stmt->execute([$email, $role, $id]);
        }
    } elseif ($action === 'delete') {
        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            // Re-index IDs
            $conn->exec("SET @count = 0; UPDATE users SET id = (@count:=@count + 1); ALTER TABLE users AUTO_INCREMENT = 1;");

            $conn->commit();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['messages']['errors'][] = "Error deleting user: " . $e->getMessage();
        }
    }

    header("Location: ../pages/admin_dashboard.php");
    exit();
} else {
    header("Location: ../pages/admin_dashboard.php");
    exit();
}
?>