<?php
// Obtener el ID del estudiante asociado al padre
$email_sesion = $_SESSION['sesion_email'];
$query_padre = $pdo->prepare("SELECT p.estudiante_id FROM usuarios as usu 
    INNER JOIN ppffs as p ON p.usuario_id = usu.id_usuario 
    WHERE usu.email = '$email_sesion'");
$query_padre->execute();
$data_padre = $query_padre->fetch(PDO::FETCH_ASSOC);
$id_estudiante = $data_padre ? $data_padre['estudiante_id'] : 0;

// Obtener datos del estudiante para mostrar en el encabezado
$query_est_datos = $pdo->prepare("SELECT per.nombres, per.apellidos FROM estudiantes est INNER JOIN personas per ON est.persona_id = per.id_persona WHERE est.id_estudiante = '$id_estudiante'");
$query_est_datos->execute();
$datos_estudiante = $query_est_datos->fetch(PDO::FETCH_ASSOC);
$nombre_estudiante = $datos_estudiante ? $datos_estudiante['nombres'].' '.$datos_estudiante['apellidos'] : 'Estudiante No Asignado';

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
                    <div class="card card-outline card-info shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #17a2b8;">
                                <i class="bi bi-eye"></i> Observador del Alumno
                            </h3>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-4 text-center">Alumno(a): <b><?=$nombre_estudiante;?></b></h4>
                            
                            <?php if(empty($observaciones)): ?>
                                <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> Los docentes aún no han registrado observaciones para este estudiante.</div>
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
