<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/db.php';

/**
 * Check if the user is logged in.
 * Redirects to the login page if the user is not logged in.
 */
function checkLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /ATIS/views/registration/login");
        exit();
    }
}

/**
 * Get the currently logged-in user.
 * Returns the user data as an associative array or null if the user is not found.
 */
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

/**
 * Check if the currently logged-in user is an admin.
 * Returns true if the user is an admin, false otherwise.
 */
function isAdmin()
{
    $user = getLoggedInUser();
    return $user && $user['role'] === 'admin';
}
