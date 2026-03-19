php
<?php require __DIR__ . '/partials/header.php'; ?>

<style>
    .cinema-room {
        background-color: #0f172a; /* Fondo oscuro premium */
        border-radius: 15px;
        padding: 2.5rem 1rem;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.8);
        overflow-x: auto;
    }
    .cinema-screen {
        width: 80%;
        height: 8px;
        background: linear-gradient(to right, #0891b2, #3b82f6);
        border-top-left-radius: 50%;
        border-top-right-radius: 50%;
        margin: 0 auto 3rem auto;
        box-shadow: 0 -10px 20px rgba(6, 182, 212, 0.7);
    }
    .seat-row {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 15px;
    }
    /* EL PASILLO: Da un margen extra justo a la mitad de los asientos (Si tienes 8 asientos, separa en el 4to) */
    .seat-row > div:nth-child(4) {
        margin-right: 50px;
    }
    .seat-label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        border: 2px solid #4b5563;
        color: #9ca3af;
        font-weight: bold;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: transparent;
    }
    /* Asiento seleccionado (Funciona con el radio button de PHP) */
    .seat-radio:checked + .seat-label {
        background-color: #06b6d4;
        border-color: #06b6d4;
        color: white;
        box-shadow: 0 0 15px #06b6d4;
        transform: scale(1.1) translateY(-5px);
    }
    .seat-label:hover:not(.seat-occupied) {
        border-color: #06b6d4;
        color: #06b6d4;
    }
    /* Asiento ocupado */
    .seat-occupied {
        background-color: #374151;
        border-color: #374151;
        color: transparent; /* Oculta el texto para que se vea como una mancha gris */
        cursor: not-allowed;
    }
    .leyenda-box { width: 20px; height: 20px; border-top-left-radius: 6px; border-top-right-radius: 6px; }
</style>

<h2 class="mb-4">Compra de tickets</h2>

<?php if (isset($_SESSION['error_compra'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error_compra']) ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_compra'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_compra']) ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="mb-3">1. Selecciona la función</h5>
                <form method="GET" action="index.php">
                    <input type="hidden" name="accion" value="compra">
                    <div class="mb-3">
                        <label class="form-label">Cartelera</label>
                        <select name="id_funcion" class="form-select" required>
                            <option value="">Seleccione una función...</option>
                            <?php foreach ($funciones as $funcion): ?>
                                <option value="<?= (int)$funcion['ID_Funcion'] ?>"
                                    <?= ($funcionSeleccionada && (int)$funcionSeleccionada['ID_Funcion'] === (int)$funcion['ID_Funcion']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($funcion['Titulo']) ?> | <?= htmlspecialchars($funcion['Hora_Funcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Ver Asientos</button>
                </form>

                <?php if ($funcionSeleccionada): ?>
                    <div class="mt-4 p-3 bg-light rounded border-start border-info border-4">
                        <p class="mb-1 text-uppercase text-muted"><small>Película seleccionada</small></p>
                        <h5 class="mb-2 fw-bold text-dark"><?= htmlspecialchars($funcionSeleccionada['Titulo']) ?></h5>
                        <p class="mb-1"><i class="bi bi-door-open"></i> <strong>Sala:</strong> <?= htmlspecialchars($funcionSeleccionada['Sala']) ?></p>
                        <p class="mb-0 text-success fs-5 fw-bold">Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?></p>
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

                    <div class="card shadow border-0 mb-4 bg-dark text-white">
                        <div class="card-body">
                            <h5 class="mb-4 text-center text-info">2. Selecciona tu butaca</h5>

                            <div class="cinema-room">
                                <div class="text-center mb-1 text-info tracking-wider"><small>PANTALLA</small></div>
                                <div class="cinema-screen"></div>

                                <?php if (empty($mapaAsientos)): ?>
                                    <p class="text-center text-danger">No hay configuración de asientos para esta sala.</p>
                                <?php else: ?>
                                    <?php foreach ($mapaAsientos as $letraFila => $asientosFila): ?>
                                        <div class="seat-row">
                                            <?php foreach ($asientosFila as $asiento): ?>
                                                <div>
                                                    <?php if ($asiento['Ocupado'] == 1): ?>
                                                        <div class="seat-label seat-occupied" title="Ocupado">
                                                            X
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="radio" name="id_asiento" id="asiento_<?= $asiento['ID_Asiento'] ?>" value="<?= $asiento['ID_Asiento'] ?>" class="d-none seat-radio" required>
                                                        <label class="seat-label" for="asiento_<?= $asiento['ID_Asiento'] ?>" title="Fila <?= $letraFila ?> - Asiento <?= $asiento['Numero_Asiento'] ?>">
                                                            <?= htmlspecialchars($letraFila . $asiento['Numero_Asiento']) ?>
                                                        </label>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="leyenda mt-5">
                                    <div><div class="leyenda-box" style="border: 2px solid #4b5563;"></div> Disponible</div>
                                    <div><div class="leyenda-box bg-info shadow"></div> Seleccionado</div>
                                    <div><div class="leyenda-box bg-secondary"></div> Ocupado</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow border-0">
                        <div class="card-body">
                            <h5 class="mb-3">3. Método de Pago</h5>
                            <div class="d-flex gap-4 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="Efectivo" checked required>
                                    <label class="form-check-label" for="efectivo">Efectivo (Boletería)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="Tarjeta">
                                    <label class="form-check-label" for="tarjeta">Tarjeta (En línea)</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-lg w-100 fw-bold text-white shadow">
                                Confirmar y Pagar Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?>
                            </button>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <div class="card shadow border-0 h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                        <h4 class="mb-3">¡Casi listo para tu película! 🍿</h4>
                        <p class="text-muted text-center mb-4">Necesitamos saber a quién entregarle los boletos. Por favor, inicia sesión o regístrate en segundos.</p>
                        <div class="d-flex gap-3">
                            <a href="index.php?accion=login" class="btn btn-dark px-4 py-2">Iniciar Sesión</a>
                            <a href="index.php?accion=registro" class="btn btn-outline-info px-4 py-2">Crear Cuenta</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card shadow border-0 h-100 bg-light">
                <div class="card-body d-flex justify-content-center align-items-center text-center py-5">
                    <h5 class="text-muted"><i class="bi bi-arrow-left me-2"></i>Selecciona una función en el panel para ver la disponibilidad de la sala.</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>