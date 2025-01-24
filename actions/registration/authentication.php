<?php
require_once '../../db.php';

/**
 *
 * @param string $email
 * @param string $password
 * @return array|null 
 */
function authenticateUser($email, $password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}

?>
