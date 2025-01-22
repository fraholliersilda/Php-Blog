<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

if (isset($_SESSION['user_id']) && isset($_POST['id'])) {
    $post_id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the post along with the user_id from the media table to check if the user is the owner
    $stmt = $conn->prepare("SELECT m.user_id
                            FROM posts p
                            LEFT JOIN media m ON p.id = m.post_id
                            WHERE p.id = :id LIMIT 1");
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // user admin or owner
        if ($is_admin || $post['user_id'] === $user_id) {
            // Delete the post from the posts table
            $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
            $delete_stmt->execute(['id' => $post_id]);

            $delete_media_stmt = $conn->prepare("DELETE FROM media WHERE post_id = :id");
            $delete_media_stmt->execute(['id' => $post_id]);

            header("Location: /ATIS/pages/posts/blog_posts.php");
            exit();
        } else {
            echo "You do not have permission to delete this post.";
        }
    } else {
        echo "Post not found.";
    }
} else {
    header("Location: /ATIS/pages/posts/blog_posts.php");
    exit();
}
?>
