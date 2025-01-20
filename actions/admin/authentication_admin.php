<?php
require_once '../db.php';

/**
 *
 * @param string $email
 * @param string $password
 * @return array|null User data if authenticated, otherwise null.
 */

function authenticateAdmin($email, $password) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT u.* 
        FROM users u
        INNER JOIN roles r ON u.role = r.id
        WHERE u.email = :email AND r.role = 'admin'
    ");
    $stmt->execute(['email' => $email]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        return $admin;
    }
    return null;
}

?>
