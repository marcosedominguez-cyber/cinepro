<?php require __DIR__ . '/partials/header.php'; ?>

<style>
    .cinema-room {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 2.5rem 1rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow-x: auto;
    }
    .cinema-screen {
        width: 80%;
        min-width: 400px;
        height: 8px;
        background: #ced4da;
        border-top-left-radius: 50%;
        border-top-right-radius: 50%;
        margin: 0 auto 3rem auto;
        box-shadow: 0 -8px 15px rgba(0, 0, 0, 0.08);
    }
    .seat-row {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 12px;
        min-width: max-content;
        padding: 0 15px;
    }
    .seat-row > div:nth-child(4) {
        margin-right: 35px;
    }
    .seat-label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border: 2px solid #adb5bd;
        color: #495057;
        font-weight: bold;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }
    /* AHORA ES CHECKBOX EN LUGAR DE RADIO */
    .seat-checkbox:checked + .seat-label {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.4);
        transform: scale(1.1) translateY(-3px);
    }
    .seat-label:hover:not(.seat-occupied) {
        border-color: #dc3545;
        color: #dc3545;
    }
    .seat-occupied {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #adb5bd;
        cursor: not-allowed;
    }
    .leyenda-box { width: 18px; height: 18px; border-top-left-radius: 5px; border-top-right-radius: 5px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2 class="text-dark">🎟️ Adquiere tus Entradas</h2>
</div>

<?php if (isset($_SESSION['error_compra'])): ?>
    <div class="alert alert-danger shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error_compra']) ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_compra'])): ?>
    <div class="alert alert-success shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['success_compra']) ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3 text-dark">1. Selecciona la función</h5>
                <form method="GET" action="index.php">
                    <input type="hidden" name="accion" value="compra">
                    <div class="mb-3">
                        <label class="form-label text-muted">Cartelera:</label>
                        <select name="id_funcion" class="form-select" required onchange="this.form.submit()">
                            <option value="">Elige una función...</option>
                            <?php foreach ($funciones as $funcion): ?>
                                <option value="<?= (int)$funcion['ID_Funcion'] ?>"
                                    <?= ($funcionSeleccionada && (int)$funcionSeleccionada['ID_Funcion'] === (int)$funcion['ID_Funcion']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($funcion['Titulo']) ?> | <?= htmlspecialchars($funcion['Hora_Funcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <?php if ($funcionSeleccionada): ?>
                    <div class="mt-4 p-3 bg-light rounded border-start border-danger border-4">
                        <p class="mb-1 text-uppercase text-muted"><small>Tu selección</small></p>
                        <h5 class="mb-2 fw-bold text-dark"><?= htmlspecialchars($funcionSeleccionada['Titulo']) ?></h5>
                        <p class="mb-1 text-dark"><strong><i class="bi bi-calendar-event me-1"></i> Fecha:</strong> <?= htmlspecialchars($funcionSeleccionada['Fecha_Funcion']) ?></p>
                        <p class="mb-1 text-dark"><strong><i class="bi bi-door-open me-1"></i> Sala:</strong> <?= htmlspecialchars($funcionSeleccionada['Sala']) ?></p>
                        <p class="mb-0 text-success fs-5 fw-bold mt-2">Bs <?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?> c/u</p>
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
                    <input type="hidden" id="precio-base" value="<?= htmlspecialchars((string)$funcionSeleccionada['Precio_Base']) ?>">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="mb-4 text-center text-dark">2. Selecciona tus butacas (Múltiples)</h5>

                            <div class="cinema-room">
                                <div class="text-center mb-1 text-muted tracking-wider"><small class="fw-bold">PANTALLA</small></div>
                                <div class="cinema-screen"></div>

                                <?php if (empty($mapaAsientos)): ?>
                                    <p class="text-center text-danger">Aún no hay asientos configurados para esta sala.</p>
                                <?php else: ?>
                                    <?php foreach ($mapaAsientos as $letraFila => $asientosFila): ?>
                                        <div class="seat-row">
                                            <?php foreach ($asientosFila as $asiento): ?>
                                                <div>
                                                    <?php if ($asiento['Ocupado'] == 1): ?>
                                                        <div class="seat-label seat-occupied" title="Ocupado">X</div>
                                                    <?php else: ?>
                                                        <input type="checkbox" name="id_asientos[]" id="asiento_<?= $asiento['ID_Asiento'] ?>" value="<?= $asiento['ID_Asiento'] ?>" class="d-none seat-checkbox" data-label="<?= htmlspecialchars($letraFila . $asiento['Numero_Asiento']) ?>">
                                                        <label class="seat-label" for="asiento_<?= $asiento['ID_Asiento'] ?>" title="Fila <?= $letraFila ?> - Asiento <?= $asiento['Numero_Asiento'] ?>">
                                                            <?= htmlspecialchars($letraFila . $asiento['Numero_Asiento']) ?>
                                                        </label>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="leyenda mt-5 d-flex justify-content-center gap-4 text-sm text-dark">
                                    <div class="d-flex align-items-center gap-2"><div class="leyenda-box" style="border: 2px solid #adb5bd;"></div> Libre</div>
                                    <div class="d-flex align-items-center gap-2"><div class="leyenda-box bg-danger shadow-sm"></div> Tu Elección</div>
                                    <div class="d-flex align-items-center gap-2"><div class="leyenda-box bg-light border"></div> Ocupado</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-5">
                        <div class="card-body">
                            <h5 class="mb-3 text-dark">3. Método de Pago</h5>
                            
                            <div class="alert alert-light border d-flex align-items-center mb-4">
                                <div class="fs-1 me-3">🎟️</div>
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">Asientos a comprar: <span id="asiento-text" class="text-muted fs-5">Ninguno</span></h6>
                                    <small class="text-muted">Total calculado: <strong id="total-precio" class="text-success fs-6">Bs 0.00</strong></small>
                                </div>
                            </div>

                            <div class="d-flex gap-4 mb-4">
                                <div class="form-check p-3 border rounded w-50 bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="metodo_pago" id="efectivo" value="Efectivo" checked required>
                                    <label class="form-check-label ms-2 text-dark fw-bold" for="efectivo">💵 Efectivo (Caja)</label>
                                </div>
                                <div class="form-check p-3 border rounded w-50 bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="metodo_pago" id="tarjeta" value="Tarjeta">
                                    <label class="form-check-label ms-2 text-dark fw-bold" for="tarjeta">💳 Tarjeta (Online)</label>
                                </div>
                            </div>
                            
                            <button type="submit" id="btn-confirmar" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm" disabled>
                                Selecciona un asiento para continuar
                            </button>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                        <h4 class="mb-3 text-dark">¡Casi listo para tu película! 🍿</h4>
                        <p class="text-muted text-center mb-4">Necesitamos saber a quién entregarle los boletos.</p>
                        <div class="d-flex gap-3">
                            <a href="index.php?accion=login" class="btn btn-dark px-4 py-2">Iniciar Sesión</a>
                            <a href="index.php?accion=registro" class="btn btn-outline-danger px-4 py-2">Crear Cuenta</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body d-flex justify-content-center align-items-center text-center py-5">
                    <h5 class="text-muted">👈 Selecciona una película en el panel.</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.seat-checkbox');
        const asientoText = document.getElementById('asiento-text');
        const totalPrecioText = document.getElementById('total-precio');
        const btnConfirmar = document.getElementById('btn-confirmar');
        
        // Lee el precio de la base de datos que guardamos en el input oculto
        const precioBaseInput = document.getElementById('precio-base');
        const precioBase = precioBaseInput ? parseFloat(precioBaseInput.value) : 0;

        checkboxes.forEach(cb => {
            cb.addEventListener('change', actualizarResumen);
        });

        function actualizarResumen() {
            // Filtra solo los checkboxes que el usuario ha marcado
            const seleccionados = Array.from(checkboxes).filter(cb => cb.checked);
            
            if (seleccionados.length > 0) {
                // Obtiene la lista de nombres (ej. "A1, A2, B4")
                const nombresAsientos = seleccionados.map(cb => cb.getAttribute('data-label')).join(', ');
                asientoText.textContent = nombresAsientos;
                asientoText.classList.replace('text-muted', 'text-danger');
                
                // Calcula el precio total
                const precioTotal = (seleccionados.length * precioBase).toFixed(2);
                totalPrecioText.textContent = 'Bs ' + precioTotal;
                
                // Habilita el botón y actualiza el texto
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = `Confirmar Compra por Bs ${precioTotal} <small>(${seleccionados.length} entradas)</small>`;
            } else {
                // Si desmarcan todo, vuelve al estado inicial
                asientoText.textContent = 'Ninguno';
                asientoText.classList.replace('text-danger', 'text-muted');
                totalPrecioText.textContent = 'Bs 0.00';
                btnConfirmar.disabled = true;
                btnConfirmar.textContent = 'Selecciona un asiento para continuar';
            }
        }
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>