<?php
require_once 'app/config.php';

try {
    $pdo->beginTransaction();

    $time_threshold = '2026-05-08 19:40:00';

    // 1. Delete PPFFs
    $stmt = $pdo->prepare("DELETE FROM ppffs WHERE fyh_creacion >= ?");
    $stmt->execute([$time_threshold]);
    $deleted_ppffs = $stmt->rowCount();
    echo "Deleted PPFFs: $deleted_ppffs\n";

    // 2. Delete Estudiantes
    $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE fyh_creacion >= ?");
    $stmt->execute([$time_threshold]);
    $deleted_estudiantes = $stmt->rowCount();
    echo "Deleted Estudiantes: $deleted_estudiantes\n";

    // 3. Delete Personas
    $stmt = $pdo->prepare("DELETE FROM personas WHERE fyh_creacion >= ? AND (profesion='ESTUDIANTE' OR profesion='EMPLEADO')");
    $stmt->execute([$time_threshold]);
    $deleted_personas = $stmt->rowCount();
    echo "Deleted Personas: $deleted_personas\n";

    // 4. Delete Usuarios
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE fyh_creacion >= ? AND (rol_id=7 OR rol_id=8)");
    $stmt->execute([$time_threshold]);
    $deleted_usuarios = $stmt->rowCount();
    echo "Deleted Usuarios: $deleted_usuarios\n";

    // To be clean, also delete assignments and schedules created at the same time?
    // Let's only delete them if they match the timestamp
    $stmt = $pdo->prepare("DELETE FROM horarios WHERE fyh_creacion >= ?");
    $stmt->execute([$time_threshold]);
    $deleted_horarios = $stmt->rowCount();
    echo "Deleted Horarios: $deleted_horarios\n";

    $stmt = $pdo->prepare("DELETE FROM asignaciones WHERE fyh_creacion >= ?");
    $stmt->execute([$time_threshold]);
    $deleted_asignaciones = $stmt->rowCount();
    echo "Deleted Asignaciones: $deleted_asignaciones\n";


    $pdo->commit();
    echo "SUCCESS: Deleted users and related records.\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
