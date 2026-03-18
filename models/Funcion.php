<?php
require_once __DIR__ . '/../config/database.php';

class Funcion
{
    private PDO $conn;
    private string $table = "Funcion";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listar(): array
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
                INNER JOIN Pelicula p ON f.ID_Pelicula = p.ID_Pelicula
                INNER JOIN Sala s ON f.ID_Sala = s.ID_Sala
                ORDER BY f.Fecha_Funcion, f.Hora_Funcion";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear(string $fecha, string $hora, float $precio, int $idPelicula, int $idSala): bool
    {
        $sql = "INSERT INTO {$this->table}
                (Fecha_Funcion, Hora_Funcion, Precio_Base, ID_Pelicula, ID_Sala)
                VALUES (:fecha, :hora, :precio, :id_pelicula, :id_sala)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':precio' => $precio,
            ':id_pelicula' => $idPelicula,
            ':id_sala' => $idSala
        ]);
    }

    public function obtenerPorId(int $idFuncion): ?array
    {
        $sql = "SELECT
                    f.ID_Funcion,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    f.Precio_Base,
                    f.ID_Sala,
                    f.ID_Pelicula,
                    p.Titulo,
                    s.Nombre AS Sala
                FROM {$this->table} f
                INNER JOIN Pelicula p ON f.ID_Pelicula = p.ID_Pelicula
                INNER JOIN Sala s ON f.ID_Sala = s.ID_Sala
                WHERE f.ID_Funcion = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idFuncion]);

        $funcion = $stmt->fetch();

        return $funcion ?: null;
    }
}