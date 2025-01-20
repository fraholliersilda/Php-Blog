<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST["id"];

    if (isset($_FILES['profile_picture'])) {
        $profilePicture = $_FILES['profile_picture'];

        try {
            if (empty($id)) {
                throw new Exception("Invalid user ID.");
            }

            // check is user has other profile pic
            $sql = "SELECT id, path FROM media WHERE user_id = :user_id ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $id);
            $stmt->execute();
            $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profilePicture['error'] === UPLOAD_ERR_OK) {
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $originalName = basename($profilePicture["name"]);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $hashName = md5(uniqid(time(), true)) . "." . $extension;
                $fileSize = $profilePicture["size"];

                if (!in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
                    throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
                }

                $targetFile = $targetDir . $hashName;

                if (!is_writable($targetDir)) {
                    throw new Exception("Uploads folder is not writable. Check permissions.");
                }

                if (!move_uploaded_file($profilePicture["tmp_name"], $targetFile)) {
                    throw new Exception("Failed to move uploaded file.");
                }

                $path = "/ATIS/uploads/" . $hashName;

                // update existing profile picture
                if ($existingProfile) {
                    $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . $existingProfile['path'];
                    if (file_exists($oldFilePath)) {
                        //remove from th server
                        unlink($oldFilePath);
                    }

                    // update table with new picture info
                    $sql = "UPDATE media SET original_name = :original_name, hash_name = :hash_name, path = :path, size = :size, extension = :extension WHERE user_id = :user_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':original_name', $originalName);
                    $stmt->bindParam(':hash_name', $hashName);
                    $stmt->bindParam(':path', $path);
                    $stmt->bindParam(':size', $fileSize);
                    $stmt->bindParam(':extension', $extension);
                    $stmt->bindParam(':user_id', $id);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update file metadata.");
                    }

                } else {
                    // insert new pic if not existing before
                    $sql = "INSERT INTO media (original_name, hash_name, path, size, extension, user_id)
                            VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':original_name', $originalName);
                    $stmt->bindParam(':hash_name', $hashName);
                    $stmt->bindParam(':path', $path);
                    $stmt->bindParam(':size', $fileSize);
                    $stmt->bindParam(':extension', $extension);
                    $stmt->bindParam(':user_id', $id);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert file metadata.");
                    }
                }

                header("Location: /ATIS/pages/profile.php");
                exit();

            } else {
                throw new Exception("File upload error code: " . $profilePicture['error']);
            }
        } catch (Exception $e) {
            $_SESSION["messages"]["errors"][] = $e->getMessage();
            header("Location: /ATIS/pages/edit_profile.php");
            exit();
        }
    }
}
?>