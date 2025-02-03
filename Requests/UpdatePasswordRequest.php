<?php

namespace Requests;

use Requests\BaseRequest;

class UpdatePasswordRequest extends BaseRequest
{
    public static function validate($data)
    {
        $rules = [
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'different:old_password']
        ];

        return self::validateRules($data, $rules);
    }
}
