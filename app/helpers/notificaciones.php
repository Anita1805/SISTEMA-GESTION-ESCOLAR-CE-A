<?php
function enviar_notificacion($pdo, $usuario_id, $titulo, $mensaje, $enlace = "#") {
    $fechaHora = date('Y-m-d H:i:s');
    $estado_de_registro = '1';

    $sentencia = $pdo->prepare('INSERT INTO notificaciones 
        (usuario_id, titulo, mensaje, leida, enlace, estado, fyh_creacion) 
        VALUES (:usuario_id, :titulo, :mensaje, 0, :enlace, :estado, :fyh_creacion)');
    
    $sentencia->bindParam(':usuario_id', $usuario_id);
    $sentencia->bindParam(':titulo', $titulo);
    $sentencia->bindParam(':mensaje', $mensaje);
    $sentencia->bindParam(':enlace', $enlace);
    $sentencia->bindParam(':estado', $estado_de_registro);
    $sentencia->bindParam(':fyh_creacion', $fechaHora);
    
    return $sentencia->execute();
}
?>
