<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Corporativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; align-items: center; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .btn-primary { background: #764ba2; border: none; padding: 0.8rem; font-weight: 600; }
        .btn-primary:hover { background: #5a3a7e; }
        .form-control { padding: 0.8rem; border-radius: 0.5rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Bienvenido</h4>
                    <p class="text-muted small">Ingresa tus credenciales para continuar</p>
                </div>
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Usuario</label>
                        <input type="text" id="username" class="form-control" placeholder="nombre.usuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Contraseña</label>
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="6LfVYoYsAAAAAADY2KewgAJEOUS82JdL_6eaVz40"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
                    <div id="responseMsg" class="alert alert-danger p-2 small text-center" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('responseMsg');
    msg.style.display = 'none';

    const captcha = grecaptcha.getResponse();
    if(!captcha) {
        msg.innerText = "Por favor verifica el captcha.";
        msg.style.display = 'block';
        return;
    }

    const formData = new FormData();
    formData.append('username', document.getElementById('username').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('g-recaptcha-response', captcha);

    try {
        const response = await fetch('<?= base_url("auth/login") ?>', {
            method: 'POST',
            body: formData
        });

        const res = await response.json();

        if (response.ok) {
            // Guardar el JWT en LocalStorage para futuras peticiones
            localStorage.setItem('token', res.token);
            localStorage.setItem('user', JSON.stringify(res.user));
            window.location.href = '<?= base_url("dashboard") ?>';
        } else {
            msg.innerText = res.messages?.error || res.error || "Error al ingresar";
            msg.style.display = 'block';
            grecaptcha.reset();
        }
    } catch (err) {
        msg.innerText = "Error de conexión con el servidor.";
        msg.style.display = 'block';
    }
});
</script>
</body>
</html>