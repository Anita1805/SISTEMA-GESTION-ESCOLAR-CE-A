<?php
include('app/config.php');

$rutas = [
    '/admin/horarios',
    '/admin/horarios/gestionar.php',
    '/admin/horarios/vista_estudiante.php',
    '/admin/horarios/vista_docente.php',
    '/admin/chats/index.php',
    '/admin/chats/chat.php',
    '/admin/material_apoyo',
    '/admin/material_apoyo/create.php',
    '/admin/material_apoyo/vista_estudiante.php',
    '/admin/kardex',
    '/admin/calificaciones',
    '/admin/calificaciones/create.php',
    '/admin/calificaciones/vista_estudiante.php',
    '/admin/calificaciones/vista_padre.php'
];

foreach ($rutas as $ruta) {
    // Verificar si existe el permiso
    $stmt = $pdo->prepare("SELECT id_permiso FROM permisos WHERE url = :url");
    $stmt->execute(['url' => $ruta]);
    $permiso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$permiso) {
        $nombre = "Permiso " . basename($ruta);
        $stmt_ins = $pdo->prepare("INSERT INTO permisos (nombre_url, url, fyh_creacion, estado) VALUES (:nombre, :url, :fyh, '1')");
        $stmt_ins->execute(['nombre' => $nombre, 'url' => $ruta, 'fyh' => date('Y-m-d H:i:s')]);
        $id_permiso = $pdo->lastInsertId();
    } else {
        $id_permiso = $permiso['id_permiso'];
    }

    // Asignar permiso a todos los roles para no bloquear a nadie por ahora (o a roles específicos)
    // Para simplificar, le damos el permiso a todos los roles activos
    $stmt_roles = $pdo->prepare("SELECT id_rol FROM roles WHERE estado = '1'");
    $stmt_roles->execute();
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

    foreach ($roles as $rol) {
        $id_rol = $rol['id_rol'];
        // Verificar si ya tiene el permiso
        $stmt_rp = $pdo->prepare("SELECT id_rol_permiso FROM roles_permisos WHERE rol_id = :rol_id AND permiso_id = :permiso_id");
        $stmt_rp->execute(['rol_id' => $id_rol, 'permiso_id' => $id_permiso]);
        if (!$stmt_rp->fetch()) {
            $stmt_ins_rp = $pdo->prepare("INSERT INTO roles_permisos (rol_id, permiso_id, fyh_creacion, estado) VALUES (:rol_id, :permiso_id, :fyh, '1')");
            $stmt_ins_rp->execute(['rol_id' => $id_rol, 'permiso_id' => $id_permiso, 'fyh' => date('Y-m-d H:i:s')]);
        }
    }
}
echo "Permisos actualizados con éxito.";
?>
