<?php

namespace App\Controllers;

use PDO;
use Exception;

class ProfileController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function viewProfile()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header("Location: /ATIS/views/registration/login");
            exit();
        }

        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT path FROM media WHERE user_id = :user_id AND photo_type = 'profile' ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $profilePicture = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profilePicture) {
                $profilePicture = ['path' => '/ATIS/uploads/default.jpg'];
            }

            $this->render('profile/profile', ['user' => $user, 'profilePicture' => $profilePicture]);
        } catch (Exception $e) {
            $_SESSION['messages']['errors'][] = $e->getMessage();
            header("Location: /ATIS/views/profile/edit");
            exit();
        }
    }


    public function editProfile()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header("Location: /ATIS/views/registration/login");
            exit();
        }

        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT path FROM media WHERE user_id = :user_id AND photo_type = 'profile' ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $profilePicture = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profilePicture) {
                $profilePicture = ['path' => '/ATIS/uploads/default.jpg'];
            }

            $this->render('profile/edit_profile', ['user' => $user, 'profilePicture' => $profilePicture]);
        } catch (Exception $e) {
            $_SESSION['messages']['errors'][] = $e->getMessage();
            header("Location: /ATIS/views/profile/profile");
            exit();
        }
    }


    public function updateUsername($data)
    {
        session_start();

        $id = $data["id"] ?? null;
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');

        try {
            if (!$id || !$username || !$email) {
                throw new Exception("All fields are required.");
            }

            $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: /ATIS/views/profile/profile");
                exit();
            } else {
                throw new Exception("Failed to update username or email.");
            }
        } catch (Exception $e) {
            $_SESSION['messages']['errors'][] = $e->getMessage();
            header("Location: /ATIS/views/profile/edit");
            exit();
        }
    }

    public function updatePassword($data)
    {
        session_start();

        $id = $data["id"] ?? null;
        $old_password = trim($data['old_password'] ?? '');
        $new_password = trim($data['new_password'] ?? '');

        try {
            if (!$id || !$old_password || !$new_password) {
                throw new Exception("All fields are required.");
            }

            $sql = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($old_password, $user['password'])) {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                $sql = "UPDATE users SET password = :password WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    header("Location: /ATIS/views/profile/profile");
                    exit();
                } else {
                    throw new Exception("Failed to update password.");
                }
            } else {
                throw new Exception("Incorrect old password.");
            }
        } catch (Exception $e) {
            $_SESSION["messages"]["errors"][] = $e->getMessage();
            header("Location: /ATIS/views/profile/edit");
            exit();
        }
    }

    public function updateProfilePicture($data, $files)
    {
        session_start();

        $id = $data["id"] ?? null;
        $profilePicture = $files['profile_picture'] ?? null;

        try {
            if (!$id || !$profilePicture) {
                throw new Exception("Invalid user ID or file missing.");
            }

            $sql = "SELECT id, path FROM media WHERE user_id = :user_id AND photo_type = 'profile' ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $id);
            $stmt->execute();
            $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

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

            if (!move_uploaded_file($profilePicture["tmp_name"], $targetFile)) {
                throw new Exception("Failed to move uploaded file.");
            }

            $path = "/ATIS/uploads/" . $hashName;

            if ($existingProfile) {
                $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . $existingProfile['path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $sql = "INSERT INTO media (original_name, hash_name, path, size, extension, user_id, photo_type)
                    VALUES (:original_name, :hash_name, :path, :size, :extension, :user_id, 'profile')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':original_name', $originalName);
            $stmt->bindParam(':hash_name', $hashName);
            $stmt->bindParam(':path', $path);
            $stmt->bindParam(':size', $fileSize);
            $stmt->bindParam(':extension', $extension);
            $stmt->bindParam(':user_id', $id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert file metadata.");
            }

            header("Location: /ATIS/views/profile/profile");
            exit();
        } catch (Exception $e) {
            $_SESSION["messages"]["errors"][] = $e->getMessage();
            header("Location: /ATIS/views/profile/edit");
            exit();
        }
    }

    private function render($view, $data = [])
    {
        extract($data);
        require BASE_PATH . "/views/{$view}.php";
    }
}
