<?php
namespace Models;

use QueryBuilder\QueryBuilder;

class Model
{
    public $table;
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function findBy($column, $value)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['*'])
            ->where($column, '=', $value)
            ->limit(1)
            ->getOne();
    }

    public function create($data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->insert($data);
    }

    public function update($id, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update($data)
            ->where('id', '=', $id)
            ->execute();
    }

    public function delete($id)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $id)
            ->delete()
            ->execute();
    }

    public function select()
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['*']);
    }
}
