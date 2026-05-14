<?php
include ('app/config.php');

$query_est = $pdo->query("SELECT est.id_estudiante, per.id_persona as est_persona_id, per.usuario_id as est_usuario_id, pf.id_ppff, pf.usuario_id as ppff_usuario_id
    FROM estudiantes est
    INNER JOIN personas per ON est.persona_id = per.id_persona
    LEFT JOIN ppffs pf ON pf.estudiante_id = est.id_estudiante
    WHERE est.id_estudiante = 73");
$data = $query_est->fetch(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
