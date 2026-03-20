<?php require __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <?php if (isset($_SESSION['error_auth'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_auth']) ?>
            </div>
            <?php unset($_SESSION['error_auth']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_auth'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_auth']) ?>
            </div>
            <?php unset($_SESSION['success_auth']); ?>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Registro de Cliente</h3>

                <form method="POST" action="index.php?accion=registrar">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input
                                type="text"
                                name="nombre"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_registro']['nombre'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input
                                type="text"
                                name="apellido"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_registro']['apellido'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo</label>
                            <input
                                type="email"
                                name="correo"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_registro']['correo'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número (opcional)</label>
                            <input
                                type="text"
                                name="numero"
                                class="form-control"
                                placeholder="Solo números y espacios"
                                value="<?= htmlspecialchars($_SESSION['old_registro']['numero'] ?? '') ?>">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input
                                type="password"
                                name="contrasena"
                                class="form-control"
                                minlength="6"
                                required>
                            <small class="text-muted">Debe tener al menos 6 caracteres.</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Registrarme</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">¿Ya tienes cuenta?</span>
                    <a href="index.php?accion=login" class="text-decoration-none">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['old_registro']); ?>

<?php require __DIR__ . '/partials/footer.php'; ?>