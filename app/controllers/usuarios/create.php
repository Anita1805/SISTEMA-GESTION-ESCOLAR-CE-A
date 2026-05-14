<?php
/**
 * Creado por PhpStorm.
 * Usuario: Ana Sofia Vega
 * Fecha: 28/11/2025
 * Hora: 20:39
 */

include ('../../../app/config.php');

session_start();

// Validación de los datos recibidos
if (!isset($_POST['rol_id'], $_POST['email'], $_POST['password'], $_POST['password_repet'])) {
    $_SESSION['mensaje'] = "Faltan datos obligatorios";
    $_SESSION['icono'] = "error";
    echo "<script>window.history.back();</script>";
    exit();
}

$rol_id = $_POST['rol_id'];
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$password_repet = $_POST['password_repet'];
$fechaHora = date("Y-m-d H:i:s");
$estado_de_registro = "1"; // Define un estado por defecto

// Verificar que las contraseñas coincidan
if ($password !== $password_repet) {
    $_SESSION['mensaje'] = "Las contraseñas no son iguales";
    $_SESSION['icono'] = "error";
    echo "<script>window.history.back();</script>";
    exit();
}

// Hash de la contraseña
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    $celular = "0";
    $profesion = "0";
    $direccion = "0";
    $pdo->beginTransaction();

    // Insertar usuario
    $sqlUsuario = 'INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) 
                   VALUES (:rol_id, :email, :password, :fyh_creacion, :estado)';

    $stmtUsuario = $pdo->prepare($sqlUsuario);
    $stmtUsuario->bindParam(':rol_id', $rol_id);
    $stmtUsuario->bindParam(':email', $email);
    $stmtUsuario->bindParam(':password', $password_hashed);
    $stmtUsuario->bindParam(':fyh_creacion', $fechaHora);
    $stmtUsuario->bindParam(':estado', $estado_de_registro);
    $stmtUsuario->execute();

    $id_usuario = $pdo->lastInsertId();

    // Insertar en la tabla personas
    $sqlPersona = 'INSERT INTO personas (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, profesion, direccion, fyh_creacion, estado) 
                   VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, :profesion, :direccion, :fyh_creacion, :estado)';

    $stmtPersona = $pdo->prepare($sqlPersona);
    $stmtPersona->bindParam(':usuario_id', $id_usuario);
    $stmtPersona->bindParam(':nombres', $email);
    $stmtPersona->bindParam(':apellidos', $email);
    $stmtPersona->bindParam(':ci', $password);
    $stmtPersona->bindParam(':fecha_nacimiento', $fechaHora);
    $stmtPersona->bindParam(':celular', $celular);
    $stmtPersona->bindParam(':profesion', $profesion);
    $stmtPersona->bindParam(':direccion', $direccion);
    $stmtPersona->bindParam(':fyh_creacion', $fechaHora);
    $stmtPersona->bindParam(':estado', $estado_de_registro);
    $stmtPersona->execute();

    $pdo->commit();

    $_SESSION['mensaje'] = "Usuario registrado correctamente";
    $_SESSION['icono'] = "success";
    header('Location: ' . APP_URL . "/admin/usuarios");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    echo "<script>window.history.back();</script>";
    exit();
}
