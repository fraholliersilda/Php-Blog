<?php
namespace Requests;

class RegistrationRequest
{
    public static function validateSignup($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } else {
            if (strlen($data['password']) < 8) {
                $errors[] = 'Password should be at least 8 characters long';
            }
            if (!preg_match('/[A-Z]/', $data['password'])) {
                $errors[] = 'Password should contain at least one uppercase letter';
            }
            if (!preg_match('/[a-z]/', $data['password'])) {
                $errors[] = 'Password should contain at least one lowercase letter';
            }
            if (!preg_match('/[0-9]/', $data['password'])) {
                $errors[] = 'Password should contain at least one digit';
            }
            if (!preg_match('/[\W_]/', $data['password'])) {
                $errors[] = 'Password should contain at least one special symbol';
            }
        }

        return $errors;
    }

    public static function validateLogin($data)
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
