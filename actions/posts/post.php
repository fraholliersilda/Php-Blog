<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

// session_start();

// Check if `id` parameter is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No post ID provided.');
}

$id = $_GET['id'];

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
        header("HTTP/1.0 404 Not Found");
        echo "Post not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
