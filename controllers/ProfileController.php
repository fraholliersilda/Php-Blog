<?php

namespace Controllers;
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Exception;
use Requests\UpdateUsernameRequest;
use Requests\UpdatePasswordRequest;
use Requests\UpdateProfilePictureRequest;
use Exceptions\ValidationException;
use Models\Media;
use Models\User;


require_once 'redirect.php';
require_once 'errorHandler.php';
require_once 'successHandler.php';

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
            $user = (new User)->findBy('id', $this->getLoggedInUser()['id']);
            $profilePicture = (new Media)->getProfilePicture($user['id']);
            
            if (!$profilePicture || !isset($profilePicture['path'])) {
                $profilePicture = ['path' => '/ATIS/uploads/default.png'];
            }
    
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
            $user =(new User)->findBy('id', $this->getLoggedInUser()['id']);
            $profilePicture = (new Media)->getProfilePicture($user['id']) ?? ['path' => '/ATIS/uploads/default.jpg'];

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

            $result =(new User)->update($id, ['username' => $username, 'email' => $email]);
            setSuccessMessages(['Profile updated!']);
            if (!$result) {
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

            $user =(new User)->findBy('id', $id);

            if (empty($user)) {
                throw new Exception("User not found.");
            }

            if (!password_verify($old_password, $user['password'])) {
                throw new Exception("Incorrect old password.");
            }

            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $result =(new User)->update($id, ['password' => $hashedPassword]);
            setSuccessMessages(['Password updated!']);

            if (!$result) {
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

            $existingProfile = (new Media)->getProfilePicture($id);

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

            (new Media)->create([
                'original_name' => $originalName,
                'hash_name' => $hashName,
                'path' => $path,
                'size' => $fileSize,
                'extension' => $extension,
                'user_id' => $id,
                'photo_type' => 'profile'
            ]);
            setSuccessMessages(['Profile photo updated!']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            setErrors([$e->getMessage()]);
            redirect("/ATIS/views/profile/edit");
        }
    }
    private function render($view, $data = [])
    {
        extract($data);
        require BASE_PATH . "/views/{$view}.php";
    }

}
