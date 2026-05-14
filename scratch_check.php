<?php
$rest = '/admin/observador';
$rutas_nuevas_permitidas = [
    '/admin/horarios',
    '/admin/horarios/index.php',
    '/admin/horarios/gestionar.php',
    '/admin/horarios/vista_estudiante.php',
    '/admin/horarios/vista_docente.php',
    '/admin/chats',
    '/admin/chats/index.php',
    '/admin/chats/chat.php',
    '/admin/material_apoyo',
    '/admin/material_apoyo/create.php',
    '/admin/material_apoyo/vista_estudiante.php',
    '/admin/kardex',
    '/admin/kardex/index.php',
    '/admin/calificaciones',
    '/admin/calificaciones/create.php',
    '/admin/calificaciones/vista_estudiante.php',
    '/admin/calificaciones/vista_padre.php',
    '/admin/estado_cuenta/paz_y_salvo.php',
    '/admin/pagos',
    '/admin/notificaciones',
    '/admin/observador',
    '/admin/observador/index.php',
    '/admin/observador/vista_padre.php',
    '/admin/observador/vista_estudiante.php'
];

$es_ruta_nueva = false;
foreach ($rutas_nuevas_permitidas as $r_nueva) {
    if (strpos($rest, $r_nueva) === 0) {
        $es_ruta_nueva = true;
        break;
    }
}
echo $es_ruta_nueva ? 'true' : 'false';
