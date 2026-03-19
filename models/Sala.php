<?php

class Sala
{
    private PDO $conn;
    private string $table = "Sala";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
    {
        $sql = "SELECT ID_Sala, Nombre, Capacidad
                FROM {$this->table}
                ORDER BY ID_Sala ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear(string $nombre, int $capacidad): bool
    {
        $sql = "INSERT INTO {$this->table} (Nombre, Capacidad)
                VALUES (:nombre, :capacidad)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nombre' => $nombre,
            ':capacidad' => $capacidad
        ]);
    }
}