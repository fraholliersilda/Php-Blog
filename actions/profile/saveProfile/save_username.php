<?php

session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST["id"];

    if (isset($_POST['username']) && isset($_POST['email'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        try {
            $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: /ATIS/pages/profile/profile.php");
                exit();
            } else {
                throw new Exception("Failed to update username or email.");
            }
        } catch (Exception $e) {
            $_SESSION['mesasges']['errors'][] = $e->getMessage();
            header("Location: /ATIS/pages/profile/edit_profile.php");
            exit();
        }
    }
}
?>