<?php
include ('app/config.php');

$query_est = $pdo->query("DESCRIBE observador_alumno");
$data = $query_est->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
