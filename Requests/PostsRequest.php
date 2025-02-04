<?php
namespace Requests;

use Requests\BaseRequest;

class PostsRequest extends BaseRequest
{
    protected static $rules = [
        'title' => ['required', 'string', 'min:3'],
        'description' => ['required', 'string', 'max:1500']
    ];

    protected static $creationRules = [
        'cover_photo' => ['required', 'file', 'image', 'maxFileSize:5']
    ];

    public static function validate($data, $isEdit = false)
    {
        $rules = self::$rules;

        if (!$isEdit) {
            $rules = array_merge($rules, self::$creationRules);
        }

        return self::validateRules($data, $rules);
    }
}
