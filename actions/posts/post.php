<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
$id = $_GET['id'];

session_start();
$user_id = $_SESSION['user_id'];

try {
    // Updated query to reflect the new relationship between posts and media
    $query = "SELECT posts.title, posts.description, media.path AS cover_photo_path
              FROM posts
              LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
              WHERE posts.id = :id AND media.user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "You don't have permission to view this post.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
