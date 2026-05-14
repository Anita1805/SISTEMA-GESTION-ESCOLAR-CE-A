<?php
$email_sesion = $_SESSION['sesion_email'];
$rol = $rol_sesion_usuario;
if($rol_sesion_usuario == "PADRE DE FAMILIA"){
    $query_padre = $pdo->prepare("SELECT p.estudiante_id FROM usuarios as usu 
        INNER JOIN ppffs as p ON p.usuario_id = usu.id_usuario 
        WHERE usu.email = '$email_sesion'");
    $query_padre->execute();
    $data_padre = $query_padre->fetch(PDO::FETCH_ASSOC);
    $id_estudiante = $data_padre ? $data_padre['estudiante_id'] : 0;
} else {
    $query_est = $pdo->prepare("SELECT est.id_estudiante FROM usuarios as usu 
        INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
        INNER JOIN estudiantes as est ON est.persona_id = per.id_persona
        WHERE usu.email = '$email_sesion'");
    $query_est->execute();
    $data_est = $query_est->fetch(PDO::FETCH_ASSOC);
    $id_estudiante = $data_est ? $data_est['id_estudiante'] : 0;
}

// Obtener grado_id del estudiante
$query_estudiante = $pdo->prepare("SELECT grado_id FROM estudiantes WHERE id_estudiante = '$id_estudiante'");
$query_estudiante->execute();
$est_data = $query_estudiante->fetch(PDO::FETCH_ASSOC);
$id_grado = $est_data ? $est_data['grado_id'] : 0;

// Obtener el horario
$query_horarios = $pdo->prepare("SELECT h.*, m.nombre_materia, per.nombres, per.apellidos FROM horarios h
    INNER JOIN materias m ON h.materia_id = m.id_materia
    INNER JOIN docentes d ON h.docente_id = d.id_docente
    INNER JOIN personas per ON d.persona_id = per.id_persona
    WHERE h.grado_id = '$id_grado' AND h.estado = '1' ORDER BY h.hora_inicio ASC");
$query_horarios->execute();
$horarios_db = $query_horarios->fetchAll(PDO::FETCH_ASSOC);

$dias = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'];
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-success shadow" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #28a745;">
                                <i class="bi bi-calendar-check"></i> Mi Horario de Clases
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Hora</th>
                                            <?php foreach($dias as $dia): ?>
                                                <th><?=$dia;?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $horas_unicas = array_unique(array_column($horarios_db, 'hora_inicio'));
                                        sort($horas_unicas);

                                        foreach($horas_unicas as $hora):
                                            $hora_format = substr($hora, 0, 5);
                                        ?>
                                        <tr>
                                            <td class="font-weight-bold align-middle bg-light"><?=$hora_format;?></td>
                                            <?php foreach($dias as $dia): 
                                                $clase_actual = null;
                                                foreach($horarios_db as $h) {
                                                    if($h['dia_semana'] == $dia && $h['hora_inicio'] == $hora) {
                                                        $clase_actual = $h;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <td>
                                                <?php if($clase_actual): ?>
                                                    <div class="p-2 rounded bg-info text-white shadow-sm" style="font-size: 0.9rem;">
                                                        <b><?=$clase_actual['nombre_materia'];?></b><br>
                                                        <small>Prof. <?=$clase_actual['apellidos'];?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if(empty($horas_unicas)): ?>
                                <div class="alert alert-info text-center mt-3"><i class="bi bi-info-circle"></i> Aún no se ha asignado un horario para este curso.</div>
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
