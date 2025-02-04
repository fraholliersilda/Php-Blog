<?php
namespace QueryBuilder;
use Database;
use PDO;
use PDOException;
class QueryBuilder
{
    private string $table;
    private string $query = '';
    private array $bindings = [];

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $columnsList = implode(', ', $columns);
        $this->query = "SELECT $columnsList FROM {$this->table}";
        return $this;
    }
    public function where(string $column, string $operator, mixed $value): self
    {
        $placeholder = "?";
        $this->query .= (str_contains($this->query, 'WHERE') ? " AND" : " WHERE") . " $column $operator $placeholder";
        $this->bindings[] = $value;
    
        error_log("WHERE Condition: $column $operator $value");
        error_log("Current Query: " . $this->query);
        error_log("Bindings: " . print_r($this->bindings, true));
    
        return $this;
    }
    

    public function insert(array $data): mixed
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($col) => ":$col", array_keys($data)));
        $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->bindings = array_combine(array_map(fn($col) => ":$col", array_keys($data)), array_values($data));

        if ($this->execute()) {
            $pdo = Database::getConnection();
            return $pdo->lastInsertId();
        }

        return false;
    }

    public function update(array $data): self
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $this->query = "UPDATE {$this->table} SET $set";

        $this->bindings = array_values($data);

        return $this;
    }


    public function delete(): self
    {
        // Ensure WHERE conditions are not lost
        if (!str_contains($this->query, "WHERE")) {
            throw new PDOException("DELETE queries must include a WHERE clause to prevent accidental full-table deletions.");
        }
    
        // Prepend DELETE FROM before the WHERE clause
        $this->query = "DELETE FROM {$this->table} " . strstr($this->query, "WHERE");
    
        return $this;
    }
    

    public function join(string $table, string $column1, string $operator, string $column2, string $type = "INNER"): static
    {
        $this->query .= " $type JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    public function leftJoin(string $table, string $column1, string $operator, string $column2): static
    {
        $this->query .= " LEFT JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    public function limit(int $count): static
    {
        $this->query .= " LIMIT $count";
        return $this;
    }

    public function execute(): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($this->query);

        return $stmt->execute($this->bindings);
    }

    public function get(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare($this->query);
            error_log("Executing Query: " . $this->query);
            error_log("Executing Bindings: " . print_r($this->bindings, true));

            $stmt->execute($this->bindings);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results ?: [];
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function getOne(): array
    {
        $results = $this->get();
        return $results ? $results[0] : [];
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

}