<?php
include ('app/config.php');

$email_sesion = 'docente1@admin.com'; // assuming
// Or just check students directly
$query = $pdo->query("SELECT est.id_estudiante, per.nombres, per.apellidos, g.curso 
    FROM estudiantes est
    INNER JOIN personas per ON est.persona_id = per.id_persona
    INNER JOIN grados g ON est.grado_id = g.id_grado
    ORDER BY per.apellidos, per.nombres");
$estudiantes = $query->fetchAll(PDO::FETCH_ASSOC);

print_r($estudiantes);
