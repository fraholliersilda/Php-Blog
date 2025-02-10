<?php
namespace Models;

use QueryBuilder\QueryBuilder;

class User extends Model
{
    public $table = 'users';

    public $fields = [
        'id',
        'username',
        'email',
        'password'
    ];

    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }

    public function findByRole($roleId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['id', 'username', 'email'])
            ->where('role', '=', $roleId)
            ->get();
    }
}
