<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Obtener el ID del estudiante asociado al padre
$email_sesion = $_SESSION['sesion_email'];
$query_padre = $pdo->prepare("SELECT p.estudiante_id FROM usuarios as usu 
    INNER JOIN ppffs as p ON p.usuario_id = usu.id_usuario 
    WHERE usu.email = '$email_sesion'");
$query_padre->execute();
$data_padre = $query_padre->fetch(PDO::FETCH_ASSOC);
$id_estudiante = $data_padre ? $data_padre['estudiante_id'] : 0;

// Obtener datos del estudiante
$query_est_datos = $pdo->prepare("SELECT per.nombres, per.apellidos, per.ci FROM estudiantes est INNER JOIN personas per ON est.persona_id = per.id_persona WHERE est.id_estudiante = '$id_estudiante'");
$query_est_datos->execute();
$datos_estudiante = $query_est_datos->fetch(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                <i class="bi bi-cash-coin"></i> Estado de Cuenta
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if(!$datos_estudiante): ?>
                                <div class="alert alert-warning">No hay un estudiante asociado a esta cuenta de padre de familia.</div>
                            <?php else: ?>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-muted">Estudiante: <strong class="text-dark"><?=$datos_estudiante['apellidos'].' '.$datos_estudiante['nombres'];?></strong></h5>
                                        <h6 class="text-muted">Carnet: <strong class="text-dark"><?=$datos_estudiante['ci'];?></strong></h6>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="paz_y_salvo.php?id_estudiante=<?=$id_estudiante;?>" target="_blank" class="btn btn-outline-primary" style="border-radius: 20px;">
                                            <i class="bi bi-printer"></i> Imprimir Paz y Salvo
                                        </a>
                                    </div>
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
