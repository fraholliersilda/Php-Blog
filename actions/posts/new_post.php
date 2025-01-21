<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $coverPhoto = $_FILES['cover_photo'];

        try {
            $originalName = basename($coverPhoto['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
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

            // Insert the cover photo into the media table
            $mediaQuery = "INSERT INTO media (original_name, hash_name, path, size, extension, user_id, photo_type)
                           VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id, 'cover')";
            $stmt = $conn->prepare($mediaQuery);
            $stmt->execute([
                ':original_name' => $originalName,
                ':hash_name' => $hashName,
                ':path' => $path,
                ':size' => $fileSize,
                ':extension' => $extension,
                ':user_id' => $user_id,
            ]);
            $coverPhotoId = $conn->lastInsertId();

            // Insert into the posts table
            $postQuery = "INSERT INTO posts (title, description, cover_photo_id)
                          VALUES (:title, :description, :cover_photo_id)";
            $stmt = $conn->prepare($postQuery);
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':cover_photo_id' => $coverPhotoId,
            ]);

            header("Location: /ATIS/pages/posts/blog_posts.php");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "No valid cover photo provided.";
    }
}
?>