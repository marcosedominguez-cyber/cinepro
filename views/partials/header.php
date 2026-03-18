<?php
$ok = $_SESSION['ok'] ?? null;
$error = $_SESSION['error'] ?? null;
$detalleCompra = $_SESSION['detalle_compra'] ?? null;

unset($_SESSION['ok'], $_SESSION['error'], $_SESSION['detalle_compra']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine Proyecto PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-app">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎬 Cine Proyecto</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Inicio</a>
            <a class="nav-link" href="index.php?accion=cartelera">Cartelera</a>
            <a class="nav-link" href="index.php?accion=compra">Compra</a>
            <a class="nav-link" href="index.php?accion=admin">Admin</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if ($ok): ?>
        <div class="alert alert-success shadow-sm"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($detalleCompra): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title">Compra registrada</h5>
                <p class="mb-1"><strong>ID Compra:</strong> <?= htmlspecialchars((string)$detalleCompra['ID_Compra']) ?></p>
                <p class="mb-1"><strong>ID Ticket:</strong> <?= htmlspecialchars((string)$detalleCompra['ID_Ticket']) ?></p>
                <p class="mb-1"><strong>Factura:</strong> <?= htmlspecialchars($detalleCompra['Numero_Factura']) ?></p>
                <p class="mb-1"><strong>Método de pago:</strong> <?= htmlspecialchars($detalleCompra['Metodo_Pago']) ?></p>
                <p class="mb-0"><strong>Total pagado:</strong> Bs <?= htmlspecialchars((string)$detalleCompra['Pago_Total']) ?></p>
            </div>
        </div>
    <?php endif; ?>