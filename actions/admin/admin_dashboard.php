<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../pages/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = intval($_POST['id']);

    if ($action === 'update_role') {
        $role = $_POST['role']; 

        // Map the role string to their int id
        $role_map = [
            'admin' => 1,
            'user' => 2
        ];

        if (isset($role_map[$role])) {
            $role_id = $role_map[$role];

            $stmt = $conn->prepare("SELECT id FROM roles WHERE id = ?");
            $stmt->execute([$role_id]);
            if ($stmt->rowCount() > 0) {
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute([$role_id, $id]);
            } else {
                $_SESSION['messages']['errors'][] = "Invalid role ID.";
            }
        } else {
            $_SESSION['messages']['errors'][] = "Invalid role name.";
        }
    } elseif ($action === 'update_password') {
        $password = trim($_POST['password']);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $id]);
    }elseif ($action === 'delete') {
        try {

            $conn->beginTransaction();
    
            if (!$conn->inTransaction()) {
                throw new Exception("Transaction not active after beginTransaction.");
            }
    
            // Delete media entries
            $stmt = $conn->prepare("DELETE FROM media WHERE user_id = ?");
            $stmt->execute([$id]);
            //Delete users
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
    
            $conn->exec("SET @count = 0; UPDATE users SET id = (@count:=@count + 1); ALTER TABLE users AUTO_INCREMENT = 1;");
    
            // Commit the transaction
            $conn->commit();
        } catch (PDOException $e) {
            // Only roll back if there's an error before commit
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
        } catch (Exception $e) {
            $_SESSION['messages']['errors'][] = "General error: " . $e->getMessage();
        }
    }
    
    
    
    

    header("Location: ../../pages/admin/admin_dashboard.php");
    exit();
} else {
    header("Location: ../../pages/admin/admin_dashboard.php");
    exit();
}
