<?php
include ('app/config.php');

$query = $pdo->query("SELECT per.nombres, per.apellidos, est.id_estudiante 
    FROM estudiantes est 
    INNER JOIN personas per ON est.persona_id = per.id_persona
    WHERE per.nombres LIKE '%Adrián%'");
$dups = $query->fetchAll(PDO::FETCH_ASSOC);

print_r($dups);
