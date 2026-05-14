<?php
include ('../../../app/config.php');
session_start();

$grado_id = $_POST['grado_id'];
$asignacion_data = $_POST['asignacion_data']; // "materia_id-docente_id"
$dia_semana = $_POST['dia_semana'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fin = $_POST['hora_fin'];

list($materia_id, $docente_id) = explode('-', $asignacion_data);

$sentencia = $pdo->prepare("INSERT INTO horarios 
        (grado_id, materia_id, docente_id, dia_semana, hora_inicio, hora_fin, fyh_creacion, estado) 
VALUES (:grado_id, :materia_id, :docente_id, :dia_semana, :hora_inicio, :hora_fin, :fyh_creacion, '1')");

$sentencia->bindParam('grado_id', $grado_id);
$sentencia->bindParam('materia_id', $materia_id);
$sentencia->bindParam('docente_id', $docente_id);
$sentencia->bindParam('dia_semana', $dia_semana);
$sentencia->bindParam('hora_inicio', $hora_inicio);
$sentencia->bindParam('hora_fin', $hora_fin);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if($sentencia->execute()){
    $_SESSION['mensaje'] = "Clase registrada en el horario correctamente.";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/horarios/gestionar.php?id_grado=".$grado_id);
}else{
    $_SESSION['mensaje'] = "Error al registrar la clase.";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/horarios/gestionar.php?id_grado=".$grado_id);
}
?>
