<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="mb-4">Compra de tickets</h2>

<?php if (isset($_SESSION['error_compra'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error_compra']) ?>
    </div>
    <?php unset($_SESSION['error_compra']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_compra'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_compra']) ?>
    </div>
    <?php unset($_SESSION['success_compra']); ?>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">1. Selecciona la función</h5>

                <form method="GET" action="index.php">
                    <input type="hidden" name="accion" value="compra">

                    <div class="mb-3">
                        <label class="form-label">Función</label>
                        <select name="id_funcion" class="form-select" required>
                            <option value="">Seleccione una función</option>
                            <?php foreach ($funciones as $funcion): ?>
                                <option value="<?= (int)$funcion['ID_Funcion'] ?>"
                                    <?= ($funcionSeleccionada && (int)$funcionSeleccionada['ID_Funcion'] === (int)$funcion['ID_Funcion']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($funcion['Titulo']) ?> |
                                    <?= htmlspecialchars($funcion['Fecha_Funcion']) ?> |
                                    <?= htmlspecialchars($funcion['Hora_Funcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Cargar función</button>
                </form>

                <?php if ($funcionSeleccionada): ?>
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Película:</strong> <?= htmlspecialchars($funcionSeleccionada['Titulo']) ?></p>
                        <p class="mb-1"><strong>Sala:</strong> <?= htmlspecialchars($funcionSeleccionada['Sala']) ?></p>
                        <p class="mb-0"><strong>Precio:</strong> Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if ($funcionSeleccionada): ?>
            <?php if (isset($_SESSION['id_cliente'])): ?>
                <form method="POST" action="index.php?accion=procesar_compra">
                    <input type="hidden" name="id_funcion" value="<?= (int)$funcionSeleccionada['ID_Funcion'] ?>">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">2. Cliente</h5>

                            <div class="alert alert-info border-0 mb-0">
                                Comprando como: <strong><?= htmlspecialchars($_SESSION['nombre_cliente']) ?></strong>
                                <br><small>Tus datos ya están cargados.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">3. Selecciona tu asiento</h5>

                            <?php if (empty($asientosDisponibles)): ?>
                                <p class="text-danger mb-0">Lo sentimos, no hay asientos disponibles para esta función.</p>
                            <?php else: ?>
                                <div class="seat-grid">
                                    <?php foreach ($asientosDisponibles as $asiento): ?>
                                        <label class="seat-card">
                                            <input type="radio" name="id_asiento" value="<?= (int)$asiento['ID_Asiento'] ?>" required>
                                            <div class="seat-box">
                                                <span class="d-block fw-bold"><?= htmlspecialchars($asiento['Fila']) ?></span>
                                                <span><?= htmlspecialchars($asiento['Numero_Asiento']) ?></span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($asientosDisponibles)): ?>
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="mb-3">4. Pago</h5>

                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="Efectivo" checked required>
                                        <label class="form-check-label" for="efectivo">Efectivo</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="Tarjeta" required>
                                        <label class="form-check-label" for="tarjeta">Tarjeta</label>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        Confirmar Compra por Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body py-5 text-center">
                        <h5 class="mb-3">Debes iniciar sesión para comprar</h5>
                        <p class="text-muted mb-4">
                            Para continuar con la compra de tickets, primero inicia sesión o crea una cuenta.
                        </p>

                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="index.php?accion=login" class="btn btn-primary">Iniciar sesión</a>
                            <a href="index.php?accion=registro" class="btn btn-outline-success">Registrarse</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center py-5">
                    <h5 class="text-muted">Selecciona una función de la izquierda para continuar</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>