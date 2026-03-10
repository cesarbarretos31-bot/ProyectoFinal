<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Corporativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Iniciar Sesión</h3>
    <form id="loginForm">
        <div class="mb-3">
            <label>Usuario</label>
            <input type="text" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" id="password" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Captcha: <span id="captchaQuestion" class="fw-bold"></span></label>
            <input type="number" id="captchaInput" class="form-control" placeholder="Resultado" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        <div id="errorMessage" class="text-danger mt-3 text-center" style="display:none;"></div>
    </form>
</div>
<script>
let captchaResult = 0;

// Generar un captcha matemático simple al cargar
function generateCaptcha() {
    const a = Math.floor(Math.random() * 10) + 1;
    const b = Math.floor(Math.random() * 10) + 1;
    captchaResult = a + b;
    document.getElementById('captchaQuestion').innerText = `${a} + ${b} = ?`;
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.style.display = 'none';

    // Validación de Captcha en el cliente antes de enviar [cite: 22, 37]
    const userCaptcha = document.getElementById('captchaInput').value;
    if (parseInt(userCaptcha) !== captchaResult) {
        errorDiv.innerText = "Captcha incorrecto.";
        errorDiv.style.display = 'block';
        return;
    }

    const data = {
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        captcha: userCaptcha
    };

    try {
        const response = await fetch('<?= base_url("auth/login") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        });

        const result = await response.json();

        if (response.ok && result.token) {
            // Guardar JWT en LocalStorage [cite: 23]
            localStorage.setItem('jwt_token', result.token);
            localStorage.setItem('user_info', JSON.stringify(result.user));
            
            // Redirigir al Menú Principal [cite: 11]
            window.location.href = '<?= base_url("dashboard") ?>';
        } else {
            // Manejo de errores (Usuario inactivo o no existe) [cite: 21, 39]
            errorDiv.innerText = result.error || "Error de autenticación";
            errorDiv.style.display = 'block';
            generateCaptcha(); // Reset captcha en error
        }
    } catch (error) {
        console.error("Error:", error);
    }
});

generateCaptcha();
</script>
</body>
</html>