<?php
include ('app/config.php');

$query_est = $pdo->prepare("SELECT est.id_estudiante, usu.email FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN estudiantes as est ON est.persona_id = per.id_persona
    WHERE est.id_estudiante = 58");
$query_est->execute();
$data_est = $query_est->fetch(PDO::FETCH_ASSOC);

print_r($data_est);
