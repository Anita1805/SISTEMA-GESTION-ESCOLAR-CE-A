<?php
include ('app/config.php');

$query_est = $pdo->query("SELECT est.id_estudiante, per.nombres, per.apellidos, pf.nombres_apellidos_ppff
    FROM estudiantes est
    INNER JOIN personas per ON est.persona_id = per.id_persona
    LEFT JOIN ppffs pf ON pf.estudiante_id = est.id_estudiante
    WHERE per.nombres LIKE '%Adrián%' OR per.apellidos LIKE '%Gil%'");
$data = $query_est->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
