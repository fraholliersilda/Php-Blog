<?php
namespace Models;

use QueryBuilder\QueryBuilder;

class Model 
{
    public $table;
    

    public function select()
    {
        return (new QueryBuilder)
                ->table($this->table)
                ->select(['id', 'username', 'email']);
    }
}