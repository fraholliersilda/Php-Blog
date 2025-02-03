<?php

namespace Controllers;

use PDO;
use Exception;
use Requests\UpdateUsernameRequest;
use Requests\UpdatePasswordRequest;
use Requests\UpdateProfilePictureRequest;
use Exceptions\ValidationException;

require_once 'redirect.php';
require_once 'errorHandler.php';

class ProfileController extends BaseController
{
    public function __construct($conn)
    {
        parent::__construct($conn);
    }

    public function viewProfile()
    {
        $this->checkLoggedIn();

        try {
            $user = $this->getLoggedInUser();

            $profilePicture = $this->getProfilePicture($user['id']);
            $this->render('profile/profile', ['user' => $user, 'profilePicture' => $profilePicture]);
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        }
    }

    public function editProfile()
    {
        $this->checkLoggedIn();

        try {
            $user = $this->getLoggedInUser();

            $profilePicture = $this->getProfilePicture($user['id']);
            $this->render('profile/edit_profile', ['user' => $user, 'profilePicture' => $profilePicture]);
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/profile");
        }
    }

    public function updateProfile($data, $files)
    {
        $id = $data["id"] ?? null;
        $action = $data['action'] ?? null;
    
        try {
            if (!$id || !$action) {
                throw new Exception("Invalid user ID or no action specified.");
            }
    
            switch ($action) {
                case 'updateUsername':
                    $this->updateUsername($data);
                    break;
    
                case 'updatePassword':
                    $this->updatePassword($data);
                    break;
    
                case 'updateProfilePicture':
                    $this->updateProfilePicture($data, $files);
                    break;
    
                default:
                    throw new Exception("Unknown action: $action");
            }
            redirect("/ATIS/views/profile/profile");
        } catch (ValidationException $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");

        }
    }
    
    private function updateUsername($data)
    {
        $id = $data["id"] ?? null;
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
    
        try {
            UpdateUsernameRequest::validate($data); 

            $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Failed to update username or email.");
            }
        } catch (ValidationException $e) {
            throw $e; 
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        }
    }
    
    private function updatePassword($data)
    {
        $id = $data["id"] ?? null;
        $old_password = trim($data['old_password'] ?? '');
        $new_password = trim($data['new_password'] ?? '');
    
        try {
            UpdatePasswordRequest::validate($data); 

            $sql = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!password_verify($old_password, $user['password'])) {
                throw new Exception("Incorrect old password.");
            }
    
        
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Failed to update password.");
            }
        } catch (ValidationException $e) {
            throw $e; 
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        }
    }
    

    private function updateProfilePicture($data, $files)
    {
        $id = $data["id"] ?? null;
        $profilePicture = $files['profile_picture'] ?? null;
    
        try {
            UpdateProfilePictureRequest::validate($data); 
    
            $originalName = basename($profilePicture["name"]);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $fileSize = $profilePicture["size"];
    
    
            $existingProfile = $this->getProfilePicture($id);
    
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/ATIS/uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
    
            $hashName = md5(uniqid(time(), true)) . "." . $extension;
            $targetFile = $targetDir . $hashName;
    
            if (!move_uploaded_file($profilePicture["tmp_name"], $targetFile)) {
                throw new Exception("Failed to move uploaded file.");
            }
    
            $path = "/ATIS/uploads/" . $hashName;
    
            if ($existingProfile && file_exists($_SERVER['DOCUMENT_ROOT'] . $existingProfile['path'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $existingProfile['path']);
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
        } catch (ValidationException $e) {
            throw $e; 
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        }
    }    

    private function getProfilePicture($userId)
    {
        $sql = "SELECT path FROM media WHERE user_id = :user_id AND photo_type = 'profile' ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $profilePicture = $stmt->fetch(PDO::FETCH_ASSOC);

        return $profilePicture ?: ['path' => '/ATIS/uploads/default.jpg'];
    }

    private function render($view, $data = [])
    {
        extract($data);
        require BASE_PATH . "/views/{$view}.php";
    }
}
