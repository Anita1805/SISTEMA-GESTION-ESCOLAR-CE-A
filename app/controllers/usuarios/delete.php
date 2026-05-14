<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 4/1/2024
 * Time: 16:04
 */
include ('../../../app/config.php');

$id_usuario = $_POST['id_usuario'];

try {
    // Iniciar la transacción para asegurar la consistencia de los datos
    $pdo->beginTransaction();

    // Primero, eliminar a la persona asociada
    $sentenciaPersona = $pdo->prepare("DELETE FROM personas WHERE usuario_id = :id_usuario");
    $sentenciaPersona->bindParam(':id_usuario', $id_usuario);
    $sentenciaPersona->execute();

    // Luego, eliminar al usuario
    $sentenciaUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
    $sentenciaUsuario->bindParam(':id_usuario', $id_usuario);

    if ($sentenciaUsuario->execute()) {
        // Confirmar la eliminación de ambas tablas
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = "Usuario y datos personales eliminados correctamente";
        $_SESSION['icono'] = "success";
        header('Location:' . APP_URL . "/admin/usuarios");
    } else {
        $pdo->rollBack(); // Revertir cambios si hay un error
        session_start();
        $_SESSION['mensaje'] = "Error al eliminar el usuario en la base de datos";
        $_SESSION['icono'] = "error";
        header('Location:' . APP_URL . "/admin/usuarios");
    }
} catch (Exception $exception) {
    $pdo->rollBack(); // Revertir cambios si hay una excepción
    session_start();
    $_SESSION['mensaje'] = "Error: No se pudo eliminar porque este registro está en uso en otras tablas";
    $_SESSION['icono'] = "error";
    header('Location:' . APP_URL . "/admin/usuarios");
}
