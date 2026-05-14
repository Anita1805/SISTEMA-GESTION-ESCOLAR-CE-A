<?php
include('../../config.php');

if(isset($_GET['id'])) {
    $id_noti = $_GET['id'];
    
    // Obtener enlace original
    $query = $pdo->prepare("SELECT enlace FROM notificaciones WHERE id_notificacion = :id");
    $query->execute(['id' => $id_noti]);
    $noti = $query->fetch(PDO::FETCH_ASSOC);
    
    if($noti) {
        // Marcar como leída
        $update = $pdo->prepare("UPDATE notificaciones SET leida = 1 WHERE id_notificacion = :id");
        $update->execute(['id' => $id_noti]);
        
        // Redirigir al enlace
        header("Location: ".$noti['enlace']);
        exit;
    }
}

// Fallback
header("Location: ".APP_URL."/admin");
exit;
