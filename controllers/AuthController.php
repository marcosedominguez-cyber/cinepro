<?php

class AuthController
{
    private Cliente $clienteModel;

    public function __construct(PDO $db)
    {
        $this->clienteModel = new Cliente($db);
    }

    public function login(): void
    {
        require __DIR__ . '/../views/login.php';
    }

    public function registro(): void
    {
        require __DIR__ . '/../views/registro.php';
    }

    public function registrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=registro');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        $_SESSION['old_registro'] = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'numero' => $numero
        ];

        try {
            $idCliente = $this->clienteModel->crear(
                $nombre,
                $apellido,
                $correo,
                $contrasena,
                $numero !== '' ? $numero : null
            );

            $_SESSION['id_cliente'] = $idCliente;
            $_SESSION['nombre_cliente'] = $nombre . ' ' . $apellido;
            unset($_SESSION['old_registro']);
            $_SESSION['success_auth'] = 'Cuenta creada correctamente.';

            header('Location: index.php?accion=compra');
            exit;
        } catch (InvalidArgumentException $e) {
            $_SESSION['error_auth'] = $e->getMessage();
            header('Location: index.php?accion=registro');
            exit;
        } catch (Throwable $e) {
            $_SESSION['error_auth'] = 'No se pudo completar el registro.';
            header('Location: index.php?accion=registro');
            exit;
        }
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=login');
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        $cliente = $this->clienteModel->autenticar($correo, $contrasena);

        if ($cliente) {
            $_SESSION['id_cliente'] = $cliente['ID_Cliente'];
            $_SESSION['nombre_cliente'] = $cliente['Nombre_Cliente'] . ' ' . $cliente['Apellido_Cliente'];
            $_SESSION['success_auth'] = 'Sesión iniciada correctamente.';

            header('Location: index.php?accion=compra');
            exit;
        }

        $_SESSION['error_auth'] = 'Correo o contraseña incorrectos.';
        header('Location: index.php?accion=login');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}