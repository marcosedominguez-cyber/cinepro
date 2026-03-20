<?php

class AdminAuthController
{
    private Administrador $adminModel;

    public function __construct(PDO $db)
    {
        $this->adminModel = new Administrador($db);
    }

    public function login(): void
    {
        require __DIR__ . '/../views/login_admin.php';
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=login_admin');
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        $admin = $this->adminModel->autenticar($correo, $contrasena);

        if ($admin) {
            $_SESSION['id_admin'] = $admin['ID_admin'];
            $_SESSION['nombre_admin'] = $admin['Nombre_completo'];
            $_SESSION['rol_admin'] = $admin['Nivel_acceso'];

            $_SESSION['success_admin'] = 'Sesión de administrador iniciada correctamente.';
            header('Location: index.php?accion=admin');
            exit;
        }

        $_SESSION['error_admin_login'] = 'Correo o contraseña de administrador incorrectos.';
        header('Location: index.php?accion=login_admin');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['id_admin']);
        unset($_SESSION['nombre_admin']);
        unset($_SESSION['rol_admin']);

        header('Location: index.php');
        exit;
    }
}