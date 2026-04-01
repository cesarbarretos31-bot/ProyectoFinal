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
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            margin: 0;
        }
        .card { border: none; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .btn-primary { background: #764ba2; border: none; padding: 0.8rem; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { background: #5a3a7e; transform: translateY(-1px); }
        .btn-primary:disabled { background: #ccc; }
        .form-control { padding: 0.8rem; border-radius: 0.5rem; }
        .g-recaptcha { display: flex; justify-content: center; margin-bottom: 1rem; }
        #responseMsg { display: none; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark">Bienvenido</h4>
                    <p class="text-muted small">Ingresa tus credenciales para continuar</p>
                </div>
                
                <form id="loginForm">
                    <div id="responseMsg" class="alert alert-danger p-2 small text-center"></div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Usuario</label>
                        <input type="text" id="username" name="usuario" class="form-control" placeholder="nombre.usuario" maxlength="20" pattern="[a-zA-Z0-9\s]+" title="Solo letras, números y espacios. Máximo 20 caracteres." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" maxlength="80" required>
                    </div>
                    
                    <div class="g-recaptcha" data-sitekey="6LfVYoYsAAAAALT4wql4uAmX68Gs2pASFoZHImE5"></div>
                    
                    <button type="submit" id="btnSubmit" class="btn btn-primary w-100">Iniciar Sesión</button>
                </form>
            </div>
            <p class="text-center text-white-50 mt-4 small">&copy; 2026 Sistema Corporativo</p>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const msg = document.getElementById('responseMsg');
    const btn = document.getElementById('btnSubmit');
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const captchaResponse = grecaptcha.getResponse();
    
    // Reset de UI
    msg.style.display = 'none';
    
    // Validación de longitud del usuario
    if (username.length > 20) {
        msg.innerText = "El usuario no puede tener más de 20 caracteres.";
        msg.style.display = 'block';
        return;
    }
    
    // Validación de patrón del usuario
    if (!/^[a-zA-Z0-9\s]+$/.test(username)) {
        msg.innerText = "El usuario solo puede contener letras, números y espacios.";
        msg.style.display = 'block';
        return;
    }
    
    // Validación de longitud de contraseña
    if (password.length > 80) {
        msg.innerText = "La contraseña no puede tener más de 80 caracteres.";
        msg.style.display = 'block';
        return;
    }
    
    // Validación de Captcha en Cliente
    if(!captchaResponse) {
        msg.innerText = "Por favor, completa el Captcha.";
        msg.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerText = "Verificando...";

    // Preparamos los datos (Coinciden con los nombres en Auth.php)
    const formData = new FormData();
    formData.append('usuario', username);
    formData.append('password', password);
    formData.append('g-recaptcha-response', captchaResponse);

    try {
        // Petición al controlador Auth::login
        const response = await fetch('<?= base_url("auth/login") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const res = await response.json();

        if (response.ok && res.status === 200) {
            // EXITOSO: El servidor ya creó la sesión de PHP.
            // NO guardamos nada en localStorage.
            window.location.href = res.redirect; 
        } else {
            // ERROR: Mostramos el mensaje que viene del controlador (Usuario inactivo, etc.)
            msg.innerText = res.msg || "Credenciales incorrectas";
            msg.style.display = 'block';
            
            // Reset de Captcha y Botón
            grecaptcha.reset();
            btn.disabled = false;
            btn.innerText = "Iniciar Sesión";
        }
    } catch (err) {
        console.error("Error de red:", err);
        msg.innerText = "Error de conexión con el servidor.";
        msg.style.display = 'block';
        btn.disabled = false;
        btn.innerText = "Iniciar Sesión";
    }
});
</script>
</body>
</html>