<?php
include ('app/config.php');

$query = $pdo->query("SELECT per.nombres, per.apellidos, COUNT(*) as c FROM estudiantes est INNER JOIN personas per ON est.persona_id = per.id_persona GROUP BY per.nombres, per.apellidos HAVING c > 1");
$dups = $query->fetchAll(PDO::FETCH_ASSOC);

print_r($dups);
