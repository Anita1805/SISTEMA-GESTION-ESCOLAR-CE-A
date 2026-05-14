<?php
require_once 'app/config.php';

try {
    // Check old users
    $stmt = $pdo->query("SELECT p.id_persona, p.nombres, p.apellidos, u.rol_id, p.fyh_creacion FROM personas p LEFT JOIN usuarios u ON p.usuario_id = u.id_usuario ORDER BY p.id_persona ASC LIMIT 50");
    $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "First 50 personas:\n";
    foreach ($personas as $p) {
        echo "{$p['id_persona']}: {$p['nombres']} {$p['apellidos']} (Rol: {$p['rol_id']}, Creado: {$p['fyh_creacion']})\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
