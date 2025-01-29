<?php
namespace Requests;

class ProfileRequest
{
    public static function validate($data, $action)
    {
        $errors = [];

        switch ($action) {
            case 'updateUsername':
                if (empty($data['username'])) {
                    $errors['username'] = 'Username is required.';
                } elseif (strlen($data['username']) < 3) {
                    $errors['username'] = 'Username must be at least 3 characters long.';
                }

                if (empty($data['email'])) {
                    $errors['email'] = 'Email is required.';
                } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Invalid email format.';
                }
                break;

            case 'updatePassword':
                if (empty($data['old_password'])) {
                    $errors['old_password'] = 'Old password is required.';
                }

                if (empty($data['new_password'])) {
                    $errors['new_password'] = 'New password is required.';
                } else {
                    if (strlen($data['new_password']) < 8) {
                        $errors['new_password'] = 'Password should be at least 8 characters long.';
                    }
                    if (!preg_match('/[A-Z]/', $data['new_password'])) {
                        $errors['new_password'] = 'Password should contain at least one uppercase letter.';
                    }
                    if (!preg_match('/[a-z]/', $data['new_password'])) {
                        $errors['new_password'] = 'Password should contain at least one lowercase letter.';
                    }
                    if (!preg_match('/[0-9]/', $data['new_password'])) {
                        $errors['new_password'] = 'Password should contain at least one digit.';
                    }
                    if (!preg_match('/[\W_]/', $data['new_password'])) {
                        $errors['new_password'] = 'Password should contain at least one special symbol.';
                    }
                }
                break;

            case 'updateProfilePicture':
                if (empty($data['profile_picture']['name'])) {
                    $errors['profile_picture'] = 'Profile picture is required.';
                } else {
                    $fileError = self::validateFile($data['profile_picture']);
                    if ($fileError) {
                        $errors['profile_picture'] = $fileError;
                    }
                }
                break;

            default:
                $errors['general'] = 'Invalid action.';
                break;
        }

        return count($errors) > 0 ? $errors : null;
    }


    public static function validateFile($file)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024; 

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            return 'Only JPG, JPEG, PNG & GIF files are allowed.';
        }

        if ($file['size'] > $maxFileSize) {
            return 'File size must be 5MB or less.';
            
        }

        return null; 
    }
}
