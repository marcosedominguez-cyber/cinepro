<?php
require_once __DIR__ . '/config/database.php';

$db = Database::connect();

$nombre = 'admin';
$correo = 'admin@cine.com';
$password = password_hash('123456', PASSWORD_DEFAULT);
$nivel = 'SuperAdmin';

$sql = "INSERT INTO administrador (Nombre_completo, Correo, Password, Nivel_acceso)
        VALUES (:nombre, :correo, :password, :nivel)";
$stmt = $db->prepare($sql);
$stmt->execute([
    ':nombre' => $nombre,
    ':correo' => $correo,
    ':password' => $password,
    ':nivel' => $nivel
]);

echo "Administrador creado correctamente.";