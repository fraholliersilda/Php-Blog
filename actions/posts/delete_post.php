<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/controlFunction.php';

checkLoggedIn();

$is_admin = isAdmin();
$user_id = $_SESSION['user_id'];

if (isset($_POST['id'])) {
    $post_id = $_POST['id'];

    $stmt = $conn->prepare("SELECT m.user_id
                            FROM posts p
                            LEFT JOIN media m ON p.id = m.post_id
                            WHERE p.id = :id LIMIT 1");
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        if ($is_admin || $post['user_id'] === $user_id) {
            $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
            $delete_stmt->execute(['id' => $post_id]);

            $delete_media_stmt = $conn->prepare("DELETE FROM media WHERE post_id = :id");
            $delete_media_stmt->execute(['id' => $post_id]);

            header("Location: /ATIS/views/posts/blog");
            exit();
        } else {
            echo "You do not have permission to delete this post.";
        }
    } else {
        echo "Post not found.";
    }
} else {
    header("Location: /ATIS/views/posts/blog");
    exit();
}
?>
