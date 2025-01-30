<?php
namespace Requests;
require_once 'BaseRequest.php';

class ProfileRequest extends BaseRequest
{
    public static function validate($data, $action)
    {
        $errors = [];

        switch ($action) {
            case 'updateUsername':
                $rules = [
                    'username' => ['required', 'string', 'min:3'],
                    'email' => ['required', 'string', 'email']
                ];
                break;

            case 'updatePassword':
                $rules = [
                    'old_password' => ['required', 'string'],
                    'new_password' => ['required', 'string', 'min:8', 'max:255']
                ];
                break;

            case 'updateProfilePicture':
                $rules = [
                    'profile_picture' => ['required', 'file', 'image', 'maxFileSize:5']
                ];
                break;

            default:
                $errors['general'] = 'Invalid action.';
                break;
        }

        return count($errors) > 0 ? $errors : self::validateRules($data, $rules);
    }
}
