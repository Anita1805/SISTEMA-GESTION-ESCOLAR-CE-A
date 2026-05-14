<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 4/1/2024
 * Time: 16:04
 */
include ('../../../app/config.php');

$id_estudiante = $_POST['id_estudiante'];


$sentencia = $pdo->prepare("DELETE FROM estudiantes where id_estudiante=:id_estudiante ");

$sentencia->bindParam('id_estudiante',$id_estudiante);

try {
    if ($sentencia->execute()) {
        session_start();
        $_SESSION['mensaje'] = "Se elimino el estudiantes de la manera correcta en la base de datos";
        $_SESSION['icono'] = "success";
        header('Location:' . APP_URL . "/admin/estudiantes");
    } else {
        session_start();
        $_SESSION['mensaje'] = "Error no se pudo eliminar en la base datos, comuniquese con el administrador";
        $_SESSION['icono'] = "error";
        header('Location:' . APP_URL . "/admin/estudiantes");
    }
}catch (Exception $exception){
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo eliminar en la base datos, porque este registro esta en otras tablas";
    $_SESSION['icono'] = "error";
    header('Location:' . APP_URL . "/admin/estudiantes");
}







