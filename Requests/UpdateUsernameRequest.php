<?php

namespace Requests;

use Requests\BaseRequest;

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

