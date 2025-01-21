<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

session_start();
$user_id = $_SESSION['user_id'];

try {
    $query = "
        SELECT posts.id, posts.title, posts.description, media.path AS cover_photo_path
        FROM posts
        LEFT JOIN media ON posts.cover_photo_id = media.id
        WHERE media.user_id = :user_id AND media.photo_type = 'cover'
        ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
