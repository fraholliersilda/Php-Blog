<?php

namespace Requests;

require_once 'BaseRequest.php';

class UpdateProfilePictureRequest extends BaseRequest
{
    public static function validate($data)
    {
        $rules = [
            'profile_picture' => ['required', 'file', 'image', 'maxFileSize:5']
        ];

        return self::validateRules($data, $rules);
    }
}
