<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/db.php';
function checkLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /ATIS/views/registration/login");
        exit();
    }
}

function getLoggedInUser()
{
    global $conn;

    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT u.*, r.role FROM users u
                                INNER JOIN roles r ON u.role = r.id
                                WHERE u.id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return null;
}

function isAdmin()
{
    $user = getLoggedInUser();
    return $user && $user['role'] === 'admin';
}

$is_admin = isAdmin();