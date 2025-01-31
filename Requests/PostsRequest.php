<?php
namespace Requests;
require_once 'BaseRequest.php';

class PostsRequest extends BaseRequest
{
    public $rules = [
        'title' => ['required', 'string', 'min:3'],
        'description' => ['required', 'string', 'max:1500'],
        'cover_photo' => ['required', 'file', 'image', 'maxFileSize:5']
    ];
    public static function validate($data)
    {
        return self::validateRules($data, (new self)->rules);
    }
}
