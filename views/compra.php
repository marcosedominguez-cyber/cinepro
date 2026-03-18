<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="mb-4">Compra de tickets</h2>

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
                                <option value="<?= $funcion['ID_Funcion'] ?>"
                                    <?= ($funcionSeleccionada && (int)$funcionSeleccionada['ID_Funcion'] === (int)$funcion['ID_Funcion']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($funcion['Titulo']) ?> |
                                    <?= htmlspecialchars($funcion['Fecha_Funcion']) ?> |
                                    <?= htmlspecialchars($funcion['Hora_Funcion']) ?> |
                                    <?= htmlspecialchars($funcion['Sala']) ?> |
                                    Bs <?= htmlspecialchars((string)$funcion['Precio_Base']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100">Ver asientos</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if ($funcionSeleccionada): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">2. Resumen de la función</h5>
                    <p class="mb-1"><strong>Película:</strong> <?= htmlspecialchars($funcionSeleccionada['Titulo']) ?></p>
                    <p class="mb-1"><strong>Fecha:</strong> <?= htmlspecialchars($funcionSeleccionada['Fecha_Funcion']) ?></p>
                    <p class="mb-1"><strong>Hora:</strong> <?= htmlspecialchars($funcionSeleccionada['Hora_Funcion']) ?></p>
                    <p class="mb-1"><strong>Sala:</strong> <?= htmlspecialchars($funcionSeleccionada['Sala']) ?></p>
                    <p class="mb-0"><strong>Precio oficial:</strong> Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?></p>
                </div>
            </div>

            <form method="POST" action="index.php?accion=procesar_compra">
                <input type="hidden" name="id_funcion" value="<?= (int)$funcionSeleccionada['ID_Funcion'] ?>">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">3. Selecciona un asiento</h5>

                        <?php if (count($asientosDisponibles) > 0): ?>
                            <div class="seat-grid">
                                <?php foreach ($asientosDisponibles as $asiento): ?>
                                    <label class="seat-card">
                                        <input
                                            type="radio"
                                            name="id_asiento"
                                            value="<?= (int)$asiento['ID_Asiento'] ?>"
                                            required
                                        >
                                        <span><?= htmlspecialchars($asiento['Fila'] . $asiento['Numero_Asiento']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-danger mb-0">No hay asientos disponibles para esta función.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (count($asientosDisponibles) > 0): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">4. Datos del cliente</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="numero" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo</label>
                                    <input type="email" name="correo" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="mb-3">5. Método de pago</h5>

                            <div class="d-flex gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="Efectivo" required>
                                    <label class="form-check-label" for="efectivo">Efectivo</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="Tarjeta" required>
                                    <label class="form-check-label" for="tarjeta">Tarjeta</label>
                                </div>
                            </div>

                            <p class="mb-3">
                                <strong>Total a pagar:</strong>
                                Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?>
                            </p>

                            <button class="btn btn-success btn-lg">Confirmar compra</button>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    Selecciona primero una función para continuar con la compra.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>