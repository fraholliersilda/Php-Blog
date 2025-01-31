<?php
namespace Requests;

class BaseRequest
{
    public static function validateRules($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $param] = explode(':', $rule);
                } else {
                    $ruleName = $rule;
                    $param = null;
                }

                $error = self::applyRule($data[$field] ?? null, $field, $ruleName, $param);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }

        // if(errors) {
        //     // put errors in session
        //     //Throw ValidationException
        // }

        return count($errors) > 0 ? $errors : null;
    }

    protected static function applyRule($value, $field, $rule, $param)
    {
        switch ($rule) {
            case 'required':
                return empty($value) ? ucfirst(str_replace('_', ' ', $field)) . ' is required.' : null;

            case 'string':
                return !is_string($value) ? ucfirst(str_replace('_', ' ', $field)) . ' must be a string.' : null;

            case 'min':
                return strlen($value) < $param ? ucfirst(str_replace('_', ' ', $field)) . " must be at least $param characters." : null;

            case 'max':
                return strlen($value) > $param ? ucfirst(str_replace('_', ' ', $field)) . " must be at most $param characters." : null;

            case 'file':
                return !isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK ? ucfirst(str_replace('_', ' ', $field)) . ' must be a file.' : null;

            case 'image':
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                return !in_array($extension, $allowedExtensions) ? ucfirst(str_replace('_', ' ', $field)) . ' must be an image file.' : null;

            case 'maxFileSize':
                $maxFileSize = $param * 1024 * 1024;
                return $_FILES[$field]['size'] > $maxFileSize ? ucfirst(str_replace('_', ' ', $field)) . " must be smaller than $param MB." : null;

            default:
                return null;
        }
    }
}
