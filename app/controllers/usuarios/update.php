<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 4/1/2024
 * Time: 20:39
 */

include('../../../app/config.php');

session_start(); // Se inicia sesión una sola vez

$id_usuario = $_POST['id_usuario'];
$rol_id = $_POST['rol_id'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_repet = $_POST['password_repet'];
$nombres = $email;
$apellidos = $email;
$ci = $password;
$fecha_nacimiento = "2000-01-01";
$celular = "0";
$profesion = "0";
$direccion = "0";
$estado_de_registro = "1"; // Estado activo

try {
    if (!empty($password)) {
        // Validar que las contraseñas coincidan
        if ($password !== $password_repet) {
            $_SESSION['mensaje'] = "Las contraseñas introducidas no son iguales";
            $_SESSION['icono'] = "error";
            echo '<script>window.history.back();</script>';
            exit;
        }

        // Hash de la nueva contraseña
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Actualizar usuario con contraseña
        $sql = "UPDATE usuarios SET rol_id=:rol_id, email=:email, password=:password, fyh_actualizacion=:fyh_actualizacion WHERE id_usuario=:id_usuario";
    } else {
        // Actualizar usuario sin cambiar la contraseña
        $sql = "UPDATE usuarios SET rol_id=:rol_id, email=:email, fyh_actualizacion=:fyh_actualizacion WHERE id_usuario=:id_usuario";
    }

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rol_id', $rol_id);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':fyh_actualizacion', $fechaHora);
    $stmt->bindParam(':id_usuario', $id_usuario);

    if (!empty($password)) {
        $stmt->bindParam(':password', $password);
    }

    if ($stmt->execute()) {
        // Verificar si ya existe un registro en la tabla personas
        $sqlCheck = "SELECT COUNT(*) FROM personas WHERE usuario_id = :usuario_id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':usuario_id', $id_usuario);
        $stmtCheck->execute();
        $existePersona = $stmtCheck->fetchColumn();

        if ($existePersona > 0) {
            // Si existe, actualizar los datos en `personas`
            $sqlPersona = "UPDATE personas 
                           SET nombres=:nombres, apellidos=:apellidos, ci=:ci, fecha_nacimiento=:fecha_nacimiento, 
                               celular=:celular, profesion=:profesion, direccion=:direccion, fyh_creacion=:fyh_creacion, 
                               estado=:estado 
                           WHERE usuario_id=:usuario_id";
        } else {
            // Si no existe, insertar un nuevo registro en `personas`
            $sqlPersona = "INSERT INTO personas (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, 
                                                 profesion, direccion, fyh_creacion, estado) 
                           VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, 
                                   :profesion, :direccion, :fyh_creacion, :estado)";
        }

        $stmtPersona = $pdo->prepare($sqlPersona);
        $stmtPersona->bindParam(':usuario_id', $id_usuario);
        $stmtPersona->bindParam(':nombres', $nombres);
        $stmtPersona->bindParam(':apellidos', $apellidos);
        $stmtPersona->bindParam(':ci', $ci);
        $stmtPersona->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmtPersona->bindParam(':celular', $celular);
        $stmtPersona->bindParam(':profesion', $profesion);
        $stmtPersona->bindParam(':direccion', $direccion);
        $stmtPersona->bindParam(':fyh_creacion', $fechaHora);
        $stmtPersona->bindParam(':estado', $estado_de_registro);

        $stmtPersona->execute();

        $_SESSION['mensaje'] = "Usuario y datos personales actualizados correctamente";
        $_SESSION['icono'] = "success";
        header('Location:' . APP_URL . "/admin/usuarios");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el usuario, comuníquese con el administrador";
        $_SESSION['icono'] = "error";
        echo '<script>window.history.back();</script>';
        exit;
    }
} catch (Exception $exception) {
    $_SESSION['mensaje'] = "Error: " . $exception->getMessage();
    $_SESSION['icono'] = "error";
    echo '<script>window.history.back();</script>';
    exit;
}
