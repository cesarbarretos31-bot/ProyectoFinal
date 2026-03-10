<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Desarrollo Web Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-container { width: 100%; max-width: 400px; padding: 15px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card p-4">
        <h3 class="text-center mb-4">Proyecto Corporativo</h3> [cite: 2]
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" id="username" class="form-control" placeholder="Ingrese su usuario" required> [cite: 20]
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" id="password" class="form-control" placeholder="********" required> [cite: 20]
            </div>

            
            <div class="mb-3 p-3 border rounded bg-light">
                <label class="form-label fw-bold">Verificación: <span id="captchaText"></span></label>
                <input type="number" id="captchaInput" class="form-control" placeholder="Resultado" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            <div id="msg" class="mt-3 text-center text-danger small" style="display:none;"></div>
        </form>
    </div>
</div>
<script>
let captchaSum = 0;

// Generador de Captcha simple
function generateCaptcha() {
    const n1 = Math.floor(Math.random() * 10) + 1;
    const n2 = Math.floor(Math.random() * 10) + 1;
    captchaSum = n1 + n2;
    document.getElementById('captchaText').innerText = `${n1} + ${n2} = ?`;
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const msgDiv = document.getElementById('msg');
    msgDiv.style.display = 'none';

    // Validación de captcha
    const userVal = parseInt(document.getElementById('captchaInput').value);
    if (userVal !== captchaSum) {
        msgDiv.innerText = "Error: El captcha es incorrecto.";
        msgDiv.style.display = 'block';
        return;
    }

    // Preparar datos para el controlador
    const formData = new FormData();
    formData.append('username', document.getElementById('username').value);
    formData.append('password', document.getElementById('password').value);

    try {
        const response = await fetch('<?= base_url("auth/login") ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (response.ok) {
            // Guardar datos en el cliente (preparando para JWT)
            alert("Acceso concedido");
            window.location.href = '<?= base_url("dashboard") ?>';
        } else {
            // Mostrar mensaje si no existe o estado inactivo
            msgDiv.innerText = result.messages.error || "Error de credenciales"; [cite: 21]
            msgDiv.style.display = 'block';
            generateCaptcha();
        }
    } catch (error) {
        msgDiv.innerText = "Error en la conexión con el servidor.";
        msgDiv.style.display = 'block';
    }
});

// Iniciar captcha al cargar
generateCaptcha();
</script>
</body>
</html>