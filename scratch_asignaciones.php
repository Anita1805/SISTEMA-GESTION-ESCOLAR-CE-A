<?php
include('app/config.php');

$data = [];

try {
    $stmt = $pdo->query("SELECT * FROM asignaciones");
    $data['asignaciones'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $data['asignaciones'] = "Error or table missing";
}

try {
    $stmt = $pdo->query("SELECT * FROM horarios");
    $data['horarios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $data['horarios'] = "Error or table missing";
}

file_put_contents('scratch_asignaciones.json', json_encode($data, JSON_PRETTY_PRINT));
?>
