<?php
session_start();

// Evitar cache del navegador
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirigir si ya hay sesión activa
if (isset($_SESSION['id_empleado'])) {
    header("Location: ../paginaPrincipal.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Inicio de Sesión</title>
    <link rel="icon" href="../ACASALogoAcerosA.png" type="image/png" sizes="16px">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../StylesGeneralesPublic.css?v=<?php echo time(); ?>">

</head>
<body>
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.replace('../paginaPrincipal.php');
            }
        });
    </script>

    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
    </header>

    <main class="center-container">
        <section class="center-section">

            <div class="card-box">
                <h2>Iniciar Sesión</h2>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="notice notice--err">
                        Contraseña o Correo incorrecto.
                    </div>
                <?php 
                    unset($_SESSION['error']); 
                endif; ?>

                <form action="Validar.php" method="POST">
                    <input class="input-field" type="email" name="correo" placeholder="Correo" required
                           pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.com$" style="margin-bottom: 15px;">
                    
                    <div class="login-password-container">
                        <input id="id_contrasena" class="input-field" type="password" name="contrasena" placeholder="Contraseña" required>
                        
                        <button type="button" class="login-toggle-btn" onclick="togglePasswordVisibility()" title="Mostrar contraseña">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    <button class="btn-primary" type="submit">Entrar</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("id_contrasena");
            var eyeIcon = document.getElementById("eye-icon");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                // Cambiar color para indicar que está visible (opcional)
                eyeIcon.style.color = "#ff8c42"; 
            } else {
                passwordInput.type = "password";
                // Volver al color original
                eyeIcon.style.color = "#666";
            }
        }
    </script>
</body>
</html>