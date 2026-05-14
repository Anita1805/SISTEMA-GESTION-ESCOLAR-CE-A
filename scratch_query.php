<?php
require_once 'app/config.php';

try {
    // Check how many students, parents, and users exist and their creation dates or IDs to differentiate
    $stmt = $pdo->query("SELECT p.id_persona, p.nombres, p.apellidos, u.rol_id, p.fyh_creacion FROM personas p LEFT JOIN usuarios u ON p.usuario_id = u.id_usuario ORDER BY p.id_persona DESC LIMIT 20");
    $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Last 20 personas:\n";
    print_r($personas);
    
    $stmt2 = $pdo->query("SELECT id_estudiante, nivel_id, grado_id, p.nombres, p.apellidos, e.fyh_creacion FROM estudiantes e JOIN personas p ON e.persona_id = p.id_persona ORDER BY id_estudiante DESC LIMIT 10");
    $estudiantes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "\nLast 10 estudiantes:\n";
    print_r($estudiantes);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
