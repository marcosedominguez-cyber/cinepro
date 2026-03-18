<?php
require_once __DIR__ . '/../config/database.php';

class Asiento
{
    private PDO $conn;
    private string $table = "Asiento";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function listarDisponiblesPorFuncion(int $idFuncion): array
    {
        $sqlSala = "SELECT ID_Sala
                    FROM Funcion
                    WHERE ID_Funcion = :id_funcion";
        $stmtSala = $this->conn->prepare($sqlSala);
        $stmtSala->execute([':id_funcion' => $idFuncion]);
        $funcion = $stmtSala->fetch();

        if (!$funcion) {
            return [];
        }

        $idSala = (int)$funcion['ID_Sala'];

        $sql = "SELECT
                    a.ID_Asiento,
                    a.Numero_Asiento,
                    a.Fila
                FROM {$this->table} a
                WHERE a.ID_Sala = :id_sala
                  AND a.ID_Asiento NOT IN (
                      SELECT tc.ID_Asiento
                      FROM Ticket_Compra tc
                      WHERE tc.ID_Funcion = :id_funcion
                  )
                ORDER BY a.Fila, CAST(a.Numero_Asiento AS UNSIGNED)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_sala' => $idSala,
            ':id_funcion' => $idFuncion
        ]);

        return $stmt->fetchAll();
    }

    public function contarPorSala(int $idSala): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM {$this->table}
                WHERE ID_Sala = :id_sala";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_sala' => $idSala]);
        $fila = $stmt->fetch();

        return (int)$fila['total'];
    }

    public function crearMasivo(int $idSala, int $cantidadFilas, int $asientosPorFila): array
    {
        $sqlSala = "SELECT ID_Sala, Nombre, Capacidad
                    FROM Sala
                    WHERE ID_Sala = :id_sala";
        $stmtSala = $this->conn->prepare($sqlSala);
        $stmtSala->execute([':id_sala' => $idSala]);
        $sala = $stmtSala->fetch();

        if (!$sala) {
            return ['ok' => false, 'mensaje' => 'La sala no existe.'];
        }

        $existentes = $this->contarPorSala($idSala);
        if ($existentes > 0) {
            return ['ok' => false, 'mensaje' => 'Esa sala ya tiene asientos registrados.'];
        }

        $totalNuevos = $cantidadFilas * $asientosPorFila;
        if ($totalNuevos > (int)$sala['Capacidad']) {
            return ['ok' => false, 'mensaje' => 'La cantidad de asientos supera la capacidad de la sala.'];
        }

        $letras = range('A', 'Z');
        if ($cantidadFilas > count($letras)) {
            return ['ok' => false, 'mensaje' => 'Solo se permiten hasta 26 filas.'];
        }

        try {
            $this->conn->beginTransaction();

            $sqlInsert = "INSERT INTO {$this->table} (Numero_Asiento, Fila, ID_Sala)
                          VALUES (:numero_asiento, :fila, :id_sala)";
            $stmtInsert = $this->conn->prepare($sqlInsert);

            for ($i = 0; $i < $cantidadFilas; $i++) {
                $fila = $letras[$i];

                for ($n = 1; $n <= $asientosPorFila; $n++) {
                    $stmtInsert->execute([
                        ':numero_asiento' => (string)$n,
                        ':fila' => $fila,
                        ':id_sala' => $idSala
                    ]);
                }
            }

            $this->conn->commit();

            return [
                'ok' => true,
                'mensaje' => "Se crearon {$totalNuevos} asientos para la sala {$sala['Nombre']}."
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['ok' => false, 'mensaje' => 'Error al crear asientos: ' . $e->getMessage()];
        }
    }
}