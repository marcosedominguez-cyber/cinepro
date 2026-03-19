<?php require __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Registro de Cliente</h3>

                <form method="POST" action="index.php?accion=registrar">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" class="form-control" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" minlength="6" required>
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

<?php require __DIR__ . '/partials/footer.php'; ?>