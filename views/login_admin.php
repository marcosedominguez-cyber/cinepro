<?php require __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">

        <?php if (isset($_SESSION['error_admin_login'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_admin_login']) ?>
            </div>
            <?php unset($_SESSION['error_admin_login']); ?>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Login Administrador</h3>

                <form method="POST" action="index.php?accion=autenticar_admin">
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Entrar como administrador</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>