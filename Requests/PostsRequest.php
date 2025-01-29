<?php
namespace Requests;

class PostsRequest
{

    // public $rules = [
    //     'title' => [
    //         'required', 'isFile'
    //     ]
    // ];
    public static function validate($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required.';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required.';
        }

        if (!isset($_FILES['cover_photo']) || $_FILES['cover_photo']['error'] !== UPLOAD_ERR_OK) {
            $errors['cover_photo'] = 'Cover photo is required.';
            ucfirst('hello world'); //Hello world
            str_replace('_', ' ', 'cover_photo');// cover photo

            ucfirst(str_replace('_', ' ', 'cover_photo')); // Cover photo
        } else {
            $fileError = self::validateFile($_FILES['cover_photo']);
            if ($fileError) {
                $errors['cover_photo'] = $fileError;
            }
        }

        return count($errors) > 0 ? $errors : null;
    }

    public static function validateFile($file)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            return 'Only JPG, JPEG, PNG & GIF files are allowed.';
        }

        if ($file['size'] > $maxFileSize) {
            return 'File size must be 5MB or less.';
        }

        return null; 
    }
}
