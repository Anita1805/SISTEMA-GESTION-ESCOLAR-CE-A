<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 25/1/2024
 * Time: 02:05
 */

include ('../../../app/config.php');

$docente_id = $_POST['docente_id'];
$fecha = $_POST['fecha'];
$estudiante_id = $_POST['estudiante_id'];
$materia_id = $_POST['materia_id'];
$observacion = $_POST['observacion'];
$nota = $_POST['nota'];

$sentencia = $pdo->prepare('INSERT INTO kardexs
        (docente_id, estudiante_id, materia_id, fecha, observacion, nota, fyh_creacion, estado)
VALUES (:docente_id,:estudiante_id,:materia_id,:fecha,:observacion,:nota,:fyh_creacion,:estado)');

$sentencia->bindParam(':docente_id',$docente_id);
$sentencia->bindParam(':estudiante_id',$estudiante_id);
$sentencia->bindParam(':materia_id',$materia_id);
$sentencia->bindParam(':fecha',$fecha);
$sentencia->bindParam(':observacion',$observacion);
$sentencia->bindParam(':nota',$nota);
$sentencia->bindParam('fyh_creacion',$fechaHora);
$sentencia->bindParam('estado',$estado_de_registro);

if($sentencia->execute()){
    echo 'success';
    session_start();
    $_SESSION['mensaje'] = "Se registro el reporte de kardex de la manera correcta en la base de datos";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/kardex");
//header('Location:' .$URL.'/');
}else{
    echo 'error al registrar a la base de datos';
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base datos, comuniquese con el administrador";
    $_SESSION['icono'] = "error";
    ?><script>window.history.back();</script><?php
}
