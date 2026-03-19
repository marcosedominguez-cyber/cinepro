<?php

class Pelicula
{
    private PDO $conn;
    private string $table = "pelicula";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
    {
        $sql = "SELECT ID_Pelicula, Titulo, Duracion, Sinopsis, Imagen
                FROM {$this->table}
                ORDER BY ID_Pelicula DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear(string $titulo, int $duracion, string $sinopsis = '', ?string $imagen = null): bool
    {
        $sql = "INSERT INTO {$this->table} (Titulo, Duracion, Sinopsis, Imagen)
                VALUES (:titulo, :duracion, :sinopsis, :imagen)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':titulo' => $titulo,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis,
            ':imagen' => $imagen
        ]);
    }
}