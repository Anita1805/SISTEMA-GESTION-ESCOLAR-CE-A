<?php
include('app/config.php');

$data = [];

$stmt = $pdo->query("SELECT id_rol, nombre_rol FROM roles");
$data['roles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id_nivel, nivel, turno FROM niveles WHERE estado='1'");
$data['niveles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id_grado, nivel_id, curso, paralelo FROM grados WHERE estado='1'");
$data['grados'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT grado_id, COUNT(*) as cant FROM estudiantes WHERE estado='1' GROUP BY grado_id");
$data['est_counts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT doc.id_docente, per.nombres, per.apellidos FROM docentes doc INNER JOIN personas per ON doc.persona_id = per.id_persona WHERE doc.estado='1'");
$data['docentes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id_materia, nombre_materia FROM materias WHERE estado='1'");
$data['materias'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents('scratch_db_state.json', json_encode($data, JSON_PRETTY_PRINT));
?>
