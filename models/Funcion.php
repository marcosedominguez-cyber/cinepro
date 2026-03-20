<?php

class Funcion
{
    private PDO $conn;
    private string $table = "funcion";

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
                    f.ID_admin,
                    p.Titulo,
                    p.Duracion,
                    s.Nombre AS Sala,
                    a.Nombre_completo AS Admin_Creador
                FROM {$this->table} f
                INNER JOIN pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                INNER JOIN sala s ON s.ID_Sala = f.ID_Sala
                LEFT JOIN administrador a ON a.ID_admin = f.ID_admin
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
                    f.ID_admin,
                    p.Titulo,
                    p.Duracion,
                    s.Nombre AS Sala,
                    a.Nombre_completo AS Admin_Creador
                FROM {$this->table} f
                INNER JOIN pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                INNER JOIN sala s ON s.ID_Sala = f.ID_Sala
                LEFT JOIN administrador a ON a.ID_admin = f.ID_admin
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
        int $idSala,
        int $idAdmin
    ): bool {
        $duracionNueva = $this->obtenerDuracionPelicula($idPelicula);

        if ($duracionNueva <= 0) {
            return false;
        }

        $nuevoInicio = new DateTime($fecha . ' ' . $hora);
        $nuevoFin = clone $nuevoInicio;
        $nuevoFin->modify('+' . ($duracionNueva + 15) . ' minutes');

        $funcionesSala = $this->obtenerFuncionesPorSalaYFecha($idSala, $fecha);

        foreach ($funcionesSala as $funcionExistente) {
            $inicioExistente = new DateTime(
                $funcionExistente['Fecha_Funcion'] . ' ' . $funcionExistente['Hora_Funcion']
            );

            $finExistente = clone $inicioExistente;
            $finExistente->modify('+' . (((int)$funcionExistente['Duracion']) + 15) . ' minutes');

            $hayCruce = ($nuevoInicio < $finExistente) && ($nuevoFin > $inicioExistente);

            if ($hayCruce) {
                return false;
            }
        }

        $sql = "INSERT INTO {$this->table} 
                (Fecha_Funcion, Hora_Funcion, Precio_Base, ID_Pelicula, ID_Sala, ID_admin)
                VALUES (:fecha, :hora, :precio, :id_pelicula, :id_sala, :id_admin)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':precio' => $precioBase,
            ':id_pelicula' => $idPelicula,
            ':id_sala' => $idSala,
            ':id_admin' => $idAdmin
        ]);
    }

    private function obtenerDuracionPelicula(int $idPelicula): int
    {
        $sql = "SELECT Duracion
                FROM pelicula
                WHERE ID_Pelicula = :id_pelicula
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_pelicula' => $idPelicula]);
        $data = $stmt->fetch();

        return $data ? (int)$data['Duracion'] : 0;
    }

    private function obtenerFuncionesPorSalaYFecha(int $idSala, string $fecha): array
    {
        $sql = "SELECT 
                    f.ID_Funcion,
                    f.Fecha_Funcion,
                    f.Hora_Funcion,
                    f.ID_Pelicula,
                    p.Duracion
                FROM {$this->table} f
                INNER JOIN pelicula p ON p.ID_Pelicula = f.ID_Pelicula
                WHERE f.ID_Sala = :id_sala
                  AND f.Fecha_Funcion = :fecha
                ORDER BY f.Hora_Funcion ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_sala' => $idSala,
            ':fecha' => $fecha
        ]);

        return $stmt->fetchAll();
    }
}