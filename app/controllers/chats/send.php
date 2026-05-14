<?php
include ('../../../app/config.php');
if (!defined('SKIP_LAYOUT')) {
    define('SKIP_LAYOUT', true);
}
include ('../../../admin/layout/parte1.php');

$destinatario_id = intval($_POST['destinatario_id'] ?? 0);
$mensaje = trim($_POST['mensaje'] ?? '');

if ($destinatario_id <= 0 || empty($mensaje)) {
    header('Location: '.APP_URL.'/admin/chats/index.php');
    exit();
}

if (!isChatContactAllowed($pdo, $id_usuario_sesion, $rol_sesion_usuario, $destinatario_id)) {
    header('Location: '.APP_URL.'/admin/chats/index.php');
    exit();
}

$remitente_id = $id_usuario_sesion;
$fechaHora = date('Y-m-d H:i:s');

$sentencia = $pdo->prepare("INSERT INTO mensajes_chat 
        (remitente_id, destinatario_id, mensaje, leido, estado, fyh_creacion) 
VALUES (:remitente_id, :destinatario_id, :mensaje, 0, '1', :fyh_creacion)");

$sentencia->bindParam(':remitente_id', $remitente_id);
$sentencia->bindParam(':destinatario_id', $destinatario_id);
$sentencia->bindParam(':mensaje', $mensaje);
$sentencia->bindParam(':fyh_creacion', $fechaHora);

if($sentencia->execute()){
    // Añadir notificacion
    $titulo_noti = "Nuevo mensaje en el Chat";
    $enlace_noti = APP_URL."/admin/chats/chat.php?id=".$remitente_id;
    $sent_noti = $pdo->prepare("INSERT INTO notificaciones (usuario_id, titulo, mensaje, leida, enlace, estado, fyh_creacion) VALUES (:usuario_id, :titulo, :mensaje, 0, :enlace, '1', :fyh)");
    $sent_noti->execute([
        'usuario_id' => $destinatario_id,
        'titulo' => $titulo_noti,
        'mensaje' => "Has recibido un nuevo mensaje",
        'enlace' => $enlace_noti,
        'fyh' => $fechaHora
    ]);

    header('Location: '.APP_URL."/admin/chats/chat.php?id=".$destinatario_id);
    exit();
}

$_SESSION['mensaje'] = "Error al enviar mensaje";
$_SESSION['icono'] = "error";
header('Location: '.APP_URL."/admin/chats/chat.php?id=".$destinatario_id);
exit();
?>
