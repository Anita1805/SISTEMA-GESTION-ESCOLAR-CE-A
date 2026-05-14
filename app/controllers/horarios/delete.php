<?php
include ('../../../app/config.php');
session_start();

$id_horario = $_POST['id_horario'];
$grado_id = $_POST['grado_id'];

$sentencia = $pdo->prepare("DELETE FROM horarios WHERE id_horario = :id_horario");
$sentencia->bindParam('id_horario', $id_horario);

if($sentencia->execute()){
    $_SESSION['mensaje'] = "Clase eliminada del horario correctamente.";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/horarios/gestionar.php?id_grado=".$grado_id);
}else{
    $_SESSION['mensaje'] = "Error al eliminar la clase.";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/horarios/gestionar.php?id_grado=".$grado_id);
}
?>
