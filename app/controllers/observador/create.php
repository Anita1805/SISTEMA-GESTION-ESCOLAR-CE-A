<?php
include ('../../../app/config.php');
session_start();

$id_estudiante = $_POST['id_estudiante'];
$id_docente = $_POST['id_docente'];
$cualidades = $_POST['cualidades'];
$debilidades = $_POST['debilidades'];

// Verificar si ya existe el registro
$sql_check = "SELECT id_observacion FROM observador_alumno WHERE docente_id = :docente_id AND estudiante_id = :estudiante_id";
$query_check = $pdo->prepare($sql_check);
$query_check->bindParam(':docente_id', $id_docente);
$query_check->bindParam(':estudiante_id', $id_estudiante);
$query_check->execute();
$existe = $query_check->fetch(PDO::FETCH_ASSOC);

if($existe) {
    // Actualizar
    $id_obs = $existe['id_observacion'];
    $sentencia = $pdo->prepare("UPDATE observador_alumno SET cualidades=:cualidades, debilidades=:debilidades, fyh_actualizacion=:fyh_actualizacion WHERE id_observacion=:id_obs");
    $sentencia->bindParam(':cualidades', $cualidades);
    $sentencia->bindParam(':debilidades', $debilidades);
    $sentencia->bindParam(':fyh_actualizacion', $fechaHora);
    $sentencia->bindParam(':id_obs', $id_obs);
} else {
    // Insertar
    $sentencia = $pdo->prepare("INSERT INTO observador_alumno (estudiante_id, docente_id, cualidades, debilidades, fyh_creacion, estado) VALUES (:estudiante_id, :docente_id, :cualidades, :debilidades, :fyh_creacion, '1')");
    $sentencia->bindParam(':estudiante_id', $id_estudiante);
    $sentencia->bindParam(':docente_id', $id_docente);
    $sentencia->bindParam(':cualidades', $cualidades);
    $sentencia->bindParam(':debilidades', $debilidades);
    $sentencia->bindParam(':fyh_creacion', $fechaHora);
}

if($sentencia->execute()){
    $_SESSION['mensaje'] = "Observador guardado correctamente.";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/observador/index.php");
}else{
    $_SESSION['mensaje'] = "Error al guardar el observador.";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/observador/create.php?id_estudiante=".$id_estudiante);
}
?>
