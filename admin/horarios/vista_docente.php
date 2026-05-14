<?php
$email_sesion = $_SESSION['sesion_email'];

// Obtener id docente logueado
$query_doc = $pdo->prepare("SELECT d.id_docente FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN docentes as d ON d.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query_doc->execute();
$data_doc = $query_doc->fetch(PDO::FETCH_ASSOC);
$id_docente = $data_doc ? $data_doc['id_docente'] : 0;

// Obtener el horario del docente (cruza todas sus materias y grados)
$query_horarios = $pdo->prepare("SELECT h.*, m.nombre_materia, g.curso, g.paralelo, n.nivel FROM horarios h
    INNER JOIN materias m ON h.materia_id = m.id_materia
    INNER JOIN grados g ON h.grado_id = g.id_grado
    INNER JOIN niveles n ON g.nivel_id = n.id_nivel
    WHERE h.docente_id = '$id_docente' AND h.estado = '1' ORDER BY h.hora_inicio ASC");
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
                    <div class="card card-outline card-primary shadow" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                <i class="bi bi-calendar-check"></i> Mi Horario Docente
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="bg-primary text-white">
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
                                                    <div class="p-2 rounded bg-warning text-dark shadow-sm" style="font-size: 0.9rem;">
                                                        <b><?=$clase_actual['nombre_materia'];?></b><br>
                                                        <small><?=$clase_actual['curso'].' "'.$clase_actual['paralelo'].'"';?></small>
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
                                <div class="alert alert-info text-center mt-3"><i class="bi bi-info-circle"></i> Aún no tienes clases programadas en el sistema.</div>
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
