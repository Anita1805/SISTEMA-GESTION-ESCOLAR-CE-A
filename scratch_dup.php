<?php
include ('app/config.php');

$id_docente = 3; // Example teacher, from previous tests where docente_id=3 was used
$sql_estudiantes = "SELECT DISTINCT est.id_estudiante, per.nombres, per.apellidos, g.curso, g.paralelo 
    FROM asignaciones a
    INNER JOIN estudiantes est ON a.grado_id = est.grado_id
    INNER JOIN personas per ON est.persona_id = per.id_persona
    INNER JOIN grados g ON est.grado_id = g.id_grado
    WHERE a.docente_id = '$id_docente'";
$query_est = $pdo->prepare($sql_estudiantes);
$query_est->execute();
$estudiantes = $query_est->fetchAll(PDO::FETCH_ASSOC);

print_r($estudiantes);
