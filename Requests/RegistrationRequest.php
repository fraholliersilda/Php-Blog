<?php
namespace Requests;
require_once 'BaseRequest.php';

class RegistrationRequest extends BaseRequest
{
    public static function validateSignup($data)
    {
        $rules = [
            'username' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:255']
        ];

        return self::validateRules($data, $rules);
    }

    public static function validateLogin($data)
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']
        ];

        return self::validateRules($data, $rules);
    }
}
