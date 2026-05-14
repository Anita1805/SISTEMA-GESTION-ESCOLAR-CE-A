<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Marcar todas como leídas al entrar a la página
$sentencia_leer = $pdo->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = :usuario_id AND leida = 0");
$sentencia_leer->bindParam(':usuario_id', $id_usuario_sesion);
$sentencia_leer->execute();

// Obtener todas las notificaciones
$query_todas = $pdo->prepare("SELECT * FROM notificaciones WHERE usuario_id = :usuario_id ORDER BY fyh_creacion DESC");
$query_todas->bindParam(':usuario_id', $id_usuario_sesion);
$query_todas->execute();
$todas_las_notificaciones = $query_todas->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                <i class="fas fa-bell"></i> Centro de Notificaciones
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if(empty($todas_las_notificaciones)): ?>
                                <div class="p-5 text-center text-muted">
                                    <i class="fas fa-bell-slash fa-4x mb-3 text-light"></i>
                                    <h4>No tienes notificaciones registradas.</h4>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($todas_las_notificaciones as $noti): 
                                        $fecha_formato = date('d de M Y, h:i A', strtotime($noti['fyh_creacion']));
                                        $icono = "fa-info-circle text-info"; // Default icon
                                        
                                        // Asignar iconos dinámicos si contienen ciertas palabras
                                        if (stripos($noti['titulo'], 'calificaci') !== false || stripos($noti['mensaje'], 'nota') !== false) {
                                            $icono = "fa-graduation-cap text-success";
                                        } else if (stripos($noti['titulo'], 'chat') !== false || stripos($noti['mensaje'], 'mensaje') !== false) {
                                            $icono = "fa-comment-dots text-primary";
                                        }
                                    ?>
                                    <a href="<?=$noti['enlace'];?>" class="list-group-item list-group-item-action p-4 border-bottom">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="d-flex">
                                                <div class="mr-4 mt-1">
                                                    <i class="fas <?=$icono;?> fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1 font-weight-bold text-dark"><?=$noti['titulo'];?></h5>
                                                    <p class="mb-1 text-muted" style="font-size: 1.05rem;"><?=$noti['mensaje'];?></p>
                                                    <small class="text-secondary"><i class="far fa-clock"></i> <?=$fecha_formato;?></small>
                                                </div>
                                            </div>
                                            <div>
                                                <i class="fas fa-chevron-right text-light"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
