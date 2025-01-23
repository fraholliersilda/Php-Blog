<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/controlFunction.php';

checkLoggedIn();

$is_admin = isAdmin();
$user_id = $_SESSION['user_id'];

if (isset($_POST['id'])) {
    $post_id = $_POST['id'];

    $stmt = $conn->prepare("
        SELECT p.*, m.user_id as media_user_id, m.path as cover_photo_path
        FROM posts p
        LEFT JOIN media m ON m.post_id = p.id AND m.photo_type = 'cover'
        WHERE p.id = :id LIMIT 1");
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        if ($is_admin || $post['media_user_id'] === $user_id) {
            $title = $_POST['title'];
            $description = $_POST['description'];

            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                $coverPhoto = $_FILES['cover_photo'];
                $extension = strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION));
                $hashName = md5(uniqid(time(), true)) . "." . $extension;
                $fileSize = $coverPhoto["size"];

                if (!in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
                    echo "Only JPG, JPEG, PNG & GIF files are allowed.";
                    exit();
                }

                $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $targetFile = $targetDir . $hashName;
                $path = "/ATIS/uploads/" . $hashName;

                if (!move_uploaded_file($coverPhoto['tmp_name'], $targetFile)) {
                    echo "Failed to upload the cover photo.";
                    exit();
                }

                $stmt = $conn->prepare("UPDATE media SET original_name = :original_name, hash_name = :hash_name, path = :path, size = :size, extension = :extension, user_id = :user_id, post_id = :post_id, photo_type = :photo_type WHERE post_id = :post_id AND photo_type = :photo_type");
                $stmt->execute([
                    ':original_name' => $coverPhoto['name'],
                    ':hash_name' => $hashName,
                    ':path' => $path,
                    ':size' => $fileSize,
                    ':extension' => $extension,
                    ':user_id' => $user_id,
                    ':post_id' => $post_id,
                    ':photo_type' => 'cover'
                ]);
            }

            $update_stmt = $conn->prepare("UPDATE posts SET title = :title, description = :description WHERE id = :id");
            $update_stmt->execute([
                'title' => $title,
                'description' => $description,
                'id' => $post_id
            ]);

            header("Location: /ATIS/views/posts/blog");
            exit();
        } else {
            echo "You do not have permission to edit this post.";
        }
    } else {
        echo "Post not found.";
    }
} else {
    header("Location: /ATIS/views/posts/blog");
    exit();
}
?>
