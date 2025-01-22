<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $mediaId = null;

    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        try {
            $coverPhoto = $_FILES['cover_photo'];
            $extension = strtolower(pathinfo($coverPhoto['name'], PATHINFO_EXTENSION));
            $hashName = md5(uniqid(time(), true)) . "." . $extension;
            $fileSize = $coverPhoto["size"];

            if (!in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
                throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
            }

            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $targetFile = $targetDir . $hashName;
            $path = "/ATIS/uploads/" . $hashName;

            if (!move_uploaded_file($coverPhoto['tmp_name'], $targetFile)) {
                throw new Exception("Failed to upload the cover photo.");
            }

            $photoType = 'cover';
            $mediaQuery = "INSERT INTO media (original_name, hash_name, path, size, extension, user_id, photo_type)
                           VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id, :photo_type)";
            $stmt = $conn->prepare($mediaQuery);
            $stmt->execute([
                ':original_name' => $coverPhoto['name'],
                ':hash_name' => $hashName,
                ':path' => $path,
                ':size' => $fileSize,
                ':extension' => $extension,
                ':user_id' => $user_id,
                ':photo_type' => $photoType
            ]);
            $mediaId = $conn->lastInsertId(); 
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error uploading the cover photo.";
    }

    if ($mediaId !== null) {
        try {
            $postQuery = "INSERT INTO posts (title, description) VALUES (:title, :description)";
            $stmt = $conn->prepare($postQuery);
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
            ]);
            $postId = $conn->lastInsertId(); 

            $relationQuery = "UPDATE media SET post_id = :post_id WHERE id = :media_id";
            $stmt = $conn->prepare($relationQuery);
            $stmt->execute([
                ':post_id' => $postId,
                ':media_id' => $mediaId
            ]);

            header("Location: /ATIS/pages/posts/blog_posts.php");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "No valid cover photo uploaded.";
    }
}
?>
