<?php
$id_estudiante = $_GET['id_estudiante'];
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Obtener id docente logueado
$email_sesion = $_SESSION['sesion_email'];
$query = $pdo->prepare("SELECT d.id_docente FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN docentes as d ON d.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query->execute();
$data_doc = $query->fetch(PDO::FETCH_ASSOC);
$id_docente = $data_doc ? $data_doc['id_docente'] : 0;

// Obtener datos del estudiante
$query_est = $pdo->prepare("SELECT per.nombres, per.apellidos FROM estudiantes est INNER JOIN personas per ON est.persona_id = per.id_persona WHERE est.id_estudiante = '$id_estudiante'");
$query_est->execute();
$datos_est = $query_est->fetch(PDO::FETCH_ASSOC);

// Cargar si ya existe un registro previo de este docente a este estudiante
$query_obs = $pdo->prepare("SELECT * FROM observador_alumno WHERE docente_id = '$id_docente' AND estudiante_id = '$id_estudiante'");
$query_obs->execute();
$obs_previa = $query_obs->fetch(PDO::FETCH_ASSOC);

$cualidades = $obs_previa ? $obs_previa['cualidades'] : '';
$debilidades = $obs_previa ? $obs_previa['debilidades'] : '';
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                Registrar Observador del Alumno
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-4 text-center">Alumno(a): <b><?=$datos_est['apellidos'].' '.$datos_est['nombres'];?></b></h5>
                            
                            <form action="<?=APP_URL;?>/app/controllers/observador/create.php" method="post">
                                <input type="hidden" name="id_estudiante" value="<?=$id_estudiante;?>">
                                <input type="hidden" name="id_docente" value="<?=$id_docente;?>">
                                
                                <div class="form-group mb-4">
                                    <label for="cualidades" class="text-success"><i class="bi bi-star"></i> Cualidades y Fortalezas</label>
                                    <textarea name="cualidades" class="form-control" rows="4" placeholder="Ej: Participa activamente, es responsable..." style="border-radius: 10px; border-left: 4px solid #28a745;"><?=$cualidades;?></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="debilidades" class="text-danger"><i class="bi bi-exclamation-circle"></i> Aspectos a Mejorar (Debilidades)</label>
                                    <textarea name="debilidades" class="form-control" rows="4" placeholder="Ej: Distraído en clase, le falta repasar álgebra..." style="border-radius: 10px; border-left: 4px solid #dc3545;"><?=$debilidades;?></textarea>
                                </div>

                                <hr>
                                <div class="text-right mt-3">
                                    <a href="index.php" class="btn btn-secondary" style="border-radius: 20px;">Cancelar</a>
                                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;"><i class="bi bi-save"></i> Guardar Observador</button>
                                </div>
                            </form>
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
