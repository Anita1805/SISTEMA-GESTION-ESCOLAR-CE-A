<?php
include('app/config.php');
// Get roles
$stmt = $pdo->query("SELECT id_rol, nombre_rol FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get levels
$stmt = $pdo->query("SELECT id_nivel, nivel, turno FROM niveles WHERE estado='1'");
$niveles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get grades
$stmt = $pdo->query("SELECT id_grado, nivel_id, curso, paralelo FROM grados WHERE estado='1'");
$grados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get student count per grade
$stmt = $pdo->query("SELECT grado_id, COUNT(*) as cant FROM estudiantes WHERE estado='1' GROUP BY grado_id");
$est_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get docentes
$stmt = $pdo->query("SELECT doc.id_docente, per.nombres, per.apellidos FROM docentes doc INNER JOIN personas per ON doc.persona_id = per.id_persona WHERE doc.estado='1'");
$docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "ROLES:\n"; print_r($roles);
echo "NIVELES:\n"; print_r($niveles);
echo "GRADOS:\n"; print_r($grados);
echo "STUDENT COUNTS PER GRADE:\n"; print_r($est_counts);
echo "DOCENTES:\n"; print_r($docentes);
?>
