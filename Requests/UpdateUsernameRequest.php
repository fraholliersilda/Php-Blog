<?php
namespace Requests;
require_once 'BaseRequest.php';

class UpdateUsernameRequest extends BaseRequest
{
    public static function validate($data)
    {
        $rules = [
            'username' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email']
        ];

        return self::validateRules($data, $rules);
    }
}