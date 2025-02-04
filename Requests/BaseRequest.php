<?php

namespace Requests;
use Exceptions\ValidationException;

class BaseRequest
{
    public static function validateRules($data, $rules)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

        if (!empty($errors)) {
            setErrors([$errors]);
            $firstError = array_values($errors)[0];
            throw new ValidationException($firstError);
        }

        return null;
    }

    protected static function applyRule($value, $field, $rule, $param)
    {
        switch ($rule) {
            case 'required':
                if (empty($value) && (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK)) {
                    return ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
                return null;

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

                case 'email':
                    return !filter_var($value, FILTER_VALIDATE_EMAIL) ? ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email address.' : null;          
                    
                    case 'different':
                        return isset($param) && $value === $param ? ucfirst(str_replace('_', ' ', $field)) . ' must be different from ' . str_replace('_', ' ', $param) . '.' : null;
                    

            default:
                return null;
        }
    }
}
