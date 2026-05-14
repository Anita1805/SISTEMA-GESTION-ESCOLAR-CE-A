<?php
require_once 'app/config.php';

try {
    $stmt = $pdo->query("SELECT p.id_persona, p.nombres, p.apellidos, u.rol_id, p.fyh_creacion FROM personas p LEFT JOIN usuarios u ON p.usuario_id = u.id_usuario WHERE p.fyh_creacion > '2026-05-08 19:40:00' ORDER BY p.id_persona ASC");
    $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Personas created after 19:40:00:\n";
    foreach ($personas as $p) {
        echo "{$p['id_persona']}: {$p['nombres']} {$p['apellidos']} (Rol: {$p['rol_id']}, Creado: {$p['fyh_creacion']})\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
