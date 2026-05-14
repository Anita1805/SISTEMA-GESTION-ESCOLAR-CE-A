<?php
include ('app/config.php');

$query_est = $pdo->query("SHOW TABLES");
$data = $query_est->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
