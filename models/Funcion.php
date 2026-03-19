<?php

class Funcion
{
    private PDO $conn;
    private string $table = "Funcion";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listarConPeliculaYSala(): array
    {
        $sql = "SELECT 
                    f.ID_Funcion,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    f.Precio_Base,
                    f.ID_Pelicula,
                    f.ID_Sala,
                    p.Titulo,
                    s.Nombre AS Sala
                FROM {$this->table} f
                INNER JOIN Pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                INNER JOIN Sala s ON s.ID_Sala = f.ID_Sala
                ORDER BY f.Fecha_Funcion ASC, f.Hora_Funcion ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $idFuncion): ?array
    {
        $sql = "SELECT 
                    f.ID_Funcion,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    f.Precio_Base,
                    f.ID_Pelicula,
                    f.ID_Sala,
                    p.Titulo,
                    s.Nombre AS Sala
                FROM {$this->table} f
                INNER JOIN Pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                INNER JOIN Sala s ON s.ID_Sala = f.ID_Sala
                WHERE f.ID_Funcion = :id_funcion
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_funcion' => $idFuncion]);
        $data = $stmt->fetch();

        return $data ?: null;
    }

    public function crear(
        string $fecha,
        string $hora,
        float $precioBase,
        int $idPelicula,
        int $idSala
    ): bool {
        $sql = "INSERT INTO {$this->table} 
                (Fecha_Funcion, Hora_Funcion, Precio_Base, ID_Pelicula, ID_Sala)
                VALUES (:fecha, :hora, :precio, :id_pelicula, :id_sala)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':precio' => $precioBase,
            ':id_pelicula' => $idPelicula,
            ':id_sala' => $idSala
        ]);
    }
}