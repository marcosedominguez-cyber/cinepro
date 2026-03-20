<?php

class Administrador
{
    private PDO $conn;
    private string $table = "administrador";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE Correo = :correo
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => trim($correo)]);
        $data = $stmt->fetch();

        return $data ?: null;
    }

    public function autenticar(string $correo, string $contrasena): ?array
    {
        $admin = $this->buscarPorCorreo($correo);

        if (!$admin) {
            return null;
        }

        if (!password_verify($contrasena, $admin['Password'])) {
            return null;
        }

        return $admin;
    }
}