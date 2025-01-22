<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

$id = $_GET['id']; 

session_start();

try {
    $query = "SELECT posts.title, posts.description, media.path AS cover_photo_path, users.username
              FROM posts
              LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
              LEFT JOIN users ON media.user_id = users.id
              WHERE posts.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
