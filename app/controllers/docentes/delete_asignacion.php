<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 23/1/2024
 * Time: 15:05
 */

include ('../../../app/config.php');

$id_asignacion = $_POST['id_asignacion'];

$sentencia = $pdo->prepare("DELETE FROM asignaciones where id_asignacion=:id_asignacion ");

$sentencia->bindParam('id_asignacion',$id_asignacion);

if($sentencia->execute()){
    session_start();
    $_SESSION['mensaje'] = "Se elimino la asignación de la manera correcta en la base de datos";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/docentes/asignacion.php");
}else{
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo eliminar en la base datos, comuniquese con el administrador";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/docentes/asignacion.php");
}
