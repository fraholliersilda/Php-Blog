<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: /ATIS/pages/registration/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: /ATIS/pages/registration/index.php");
    exit;
}

// fetch latest picture
$sql = "SELECT path, hash_name FROM media WHERE user_id = :user_id ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$media = $stmt->fetch(PDO::FETCH_ASSOC);

//profile picture path
$profilePicture = $media ? $media['path'] : '/ATIS/uploads/default.jpg';
?>
