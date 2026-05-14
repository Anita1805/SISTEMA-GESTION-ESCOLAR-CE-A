<?php
include ('app/config.php');

$query_est = $pdo->query("SELECT est.id_estudiante, per.nombres, per.apellidos, usu.email, pp.nombres as padre_nombres, pp.apellidos as padre_apellidos, up.email as padre_email, pf.id_ppff
    FROM estudiantes est
    INNER JOIN personas per ON est.persona_id = per.id_persona
    LEFT JOIN usuarios usu ON per.usuario_id = usu.id_usuario
    LEFT JOIN ppffs pf ON pf.estudiante_id = est.id_estudiante
    LEFT JOIN personas pp ON pf.persona_id = pp.id_persona
    LEFT JOIN usuarios up ON pp.usuario_id = up.id_usuario
    WHERE per.nombres LIKE '%Adrián%' OR per.apellidos LIKE '%Gil%' OR pp.nombres LIKE '%Alba%' OR pp.apellidos LIKE '%Jimenez%'");
$data = $query_est->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
