<?php

namespace Models;

class User extends Model
{
    public $table = 'users';

    public $fields = [
        'username',
        'email',
        'password'
    ];
}