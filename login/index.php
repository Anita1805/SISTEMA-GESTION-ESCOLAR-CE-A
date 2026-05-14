<?php
include ('../app/config.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_NAME;?></title>

    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/fontawesome-free/css/all.min.css">
    <!-- Custom Login CSS -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/css/login.css">
    <!-- Sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="login-card">
    <img src="<?=APP_URL;?>/public/images/logo_real.png" alt="Logo GEA" class="logo-main" style="mix-blend-mode: multiply; width: 220px;">
    
    <div class="login-form-container">
        <div class="login-header">
            <h1>CE-A</h1>
            <p>Portal de Gestión Escolar</p>
        </div>
        
        <form action="controller_login.php" method="post">
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="email" name="email" placeholder="Correo Institucional / DNI" required autofocus>
            </div>
            
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            
            <div class="options">
                <label><input type="checkbox" name="remember"> Recordarme</label>
                <a href="#">He olvidado mi contraseña</a>
            </div>
            
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>

        <div class="footer-links">
            <a href="#">Registrarse</a>
            <a href="#">Ayuda</a>
        </div>
    </div>
    
    <div class="copyright">
        © 2024 CE A Institution
    </div>
</div>

<?php
session_start();
if(isset($_SESSION['mensaje'])){
    $mensaje = $_SESSION['mensaje'];
    $icono = isset($_SESSION['icono']) ? $_SESSION['icono'] : 'error';
    ?>
    <script>
        Swal.fire({
            icon: "<?=$icono;?>",
            title: "<?=$mensaje;?>",
            confirmButtonColor: '#8e1818'
        });
    </script>
<?php
    unset($_SESSION['mensaje']);
    unset($_SESSION['icono']);
}
?>

<!-- jQuery -->
<script src="<?=APP_URL;?>/public/plugins/jquery/jquery.min.js"></script>
</body>
</html>
