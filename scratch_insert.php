<?php
include ('app/config.php');

$sql = "INSERT INTO observador_alumno (estudiante_id, docente_id, cualidades, debilidades, fyh_creacion, estado) 
        VALUES (73, 3, 'Es muy participativo en clase', 'Debe mejorar su puntualidad', NOW(), 1)";
$pdo->exec($sql);

echo "Observation inserted for Adrian";
