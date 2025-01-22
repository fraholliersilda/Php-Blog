<?php
function fetchUsersByRole($conn, $role) {
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE role = (SELECT id FROM roles WHERE role = :role)");
    $stmt->execute(['role' => $role]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
