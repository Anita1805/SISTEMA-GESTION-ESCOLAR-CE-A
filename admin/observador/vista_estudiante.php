<?php
// Obtener el ID del estudiante logueado
$email_sesion = $_SESSION['sesion_email'];
$query_est = $pdo->prepare("SELECT est.id_estudiante, per.nombres, per.apellidos FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN estudiantes as est ON est.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query_est->execute();
$data_est = $query_est->fetch(PDO::FETCH_ASSOC);
$id_estudiante = $data_est ? $data_est['id_estudiante'] : 0;
$nombre_estudiante = $data_est ? $data_est['nombres'].' '.$data_est['apellidos'] : 'Estudiante No Asignado';

// Obtener observaciones
$sql_obs = "SELECT o.*, p.nombres as docente_nombres, p.apellidos as docente_apellidos FROM observador_alumno o
    INNER JOIN docentes d ON o.docente_id = d.id_docente
    INNER JOIN personas p ON d.persona_id = p.id_persona
    WHERE o.estudiante_id = '$id_estudiante'";
$query_obs = $pdo->prepare($sql_obs);
$query_obs->execute();
$observaciones = $query_obs->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-success shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #28a745;">
                                <i class="bi bi-eye"></i> Mi Observador
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if(empty($observaciones)): ?>
                                <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> Aún no tienes observaciones registradas por tus docentes.</div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach($observaciones as $obs): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 shadow-sm border-0">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0 font-weight-bold text-muted"><i class="bi bi-person-badge"></i> Prof. <?=$obs['docente_apellidos'].' '.$obs['docente_nombres'];?></h6>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="text-success font-weight-bold"><i class="bi bi-star"></i> Cualidades y Fortalezas:</h6>
                                                <p class="text-muted" style="border-left: 3px solid #28a745; padding-left: 10px; font-style: italic;"><?=$obs['cualidades'] ?: 'Sin registro';?></p>
                                                
                                                <h6 class="text-danger font-weight-bold mt-4"><i class="bi bi-exclamation-circle"></i> Aspectos a Mejorar:</h6>
                                                <p class="text-muted" style="border-left: 3px solid #dc3545; padding-left: 10px; font-style: italic;"><?=$obs['debilidades'] ?: 'Sin registro';?></p>
                                            </div>
                                            <div class="card-footer bg-white border-0 text-right">
                                                <small class="text-muted">Actualizado: <?=$obs['fyh_actualizacion'] ?: $obs['fyh_creacion'];?></small>
                                            </div>
                                        </div>
                                    </div>
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
