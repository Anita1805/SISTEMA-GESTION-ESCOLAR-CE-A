<?php
$id_grado = $_GET['id_grado'];
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Obtener datos del grado
$query_grado = $pdo->prepare("SELECT g.curso, g.paralelo, n.nivel FROM grados g INNER JOIN niveles n ON g.nivel_id = n.id_nivel WHERE g.id_grado = '$id_grado'");
$query_grado->execute();
$grado = $query_grado->fetch(PDO::FETCH_ASSOC);

// Obtener todas las materias y docentes asignados a este grado
$query_asignaciones = $pdo->prepare("SELECT a.id_asignacion, m.id_materia, m.nombre_materia, d.id_docente, per.nombres, per.apellidos 
    FROM asignaciones a
    INNER JOIN materias m ON a.materia_id = m.id_materia
    INNER JOIN docentes d ON a.docente_id = d.id_docente
    INNER JOIN personas per ON d.persona_id = per.id_persona
    WHERE a.grado_id = '$id_grado' AND a.estado = '1'");
$query_asignaciones->execute();
$asignaciones = $query_asignaciones->fetchAll(PDO::FETCH_ASSOC);

// Obtener el horario actual
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
                    <div class="card card-outline card-primary shadow" style="border-radius: 15px;">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                <i class="bi bi-calendar-week"></i> Horario: <?=$grado['nivel'].' - '.$grado['curso'].' "'.$grado['paralelo'].'"';?>
                            </h3>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddHorario" style="border-radius: 20px;">
                                <i class="bi bi-plus-circle"></i> Agregar Clase
                            </button>
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
                                        // Agrupar horarios por hora_inicio para estructurar la tabla
                                        $horas_unicas = array_unique(array_column($horarios_db, 'hora_inicio'));
                                        sort($horas_unicas);

                                        foreach($horas_unicas as $hora):
                                            $hora_format = substr($hora, 0, 5);
                                        ?>
                                        <tr>
                                            <td class="font-weight-bold align-middle bg-light"><?=$hora_format;?></td>
                                            <?php foreach($dias as $dia): 
                                                // Buscar si hay clase en este dia y hora
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
                                                        <small><?=$clase_actual['apellidos'].' '.$clase_actual['nombres'];?></small><br>
                                                        <small><?=$clase_actual['hora_inicio'].' - '.$clase_actual['hora_fin'];?></small>
                                                        <br>
                                                        <form action="<?=APP_URL;?>/app/controllers/horarios/delete.php" method="post" class="mt-1">
                                                            <input type="hidden" name="id_horario" value="<?=$clase_actual['id_horario'];?>">
                                                            <input type="hidden" name="grado_id" value="<?=$id_grado;?>">
                                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Está seguro de eliminar esta clase?');" style="border-radius: 5px; padding: 2px 5px;">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </form>
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
                                <div class="alert alert-warning text-center mt-3"><i class="bi bi-info-circle"></i> No hay clases registradas en este horario.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAddHorario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="exampleModalLabel">Registrar Clase en Horario</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="<?=APP_URL;?>/app/controllers/horarios/create.php" method="post">
            <input type="hidden" name="grado_id" value="<?=$id_grado;?>">
            
            <div class="form-group">
                <label>Materia (Basado en Asignaciones Previas)</label>
                <select name="asignacion_data" class="form-control" required>
                    <option value="">Seleccione Materia y Docente...</option>
                    <?php foreach($asignaciones as $asig): ?>
                        <option value="<?=$asig['id_materia'].'-'.$asig['id_docente'];?>">
                            <?=$asig['nombre_materia'];?> (Prof. <?=$asig['apellidos'];?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Día de la Semana</label>
                <select name="dia_semana" class="form-control" required>
                    <?php foreach($dias as $dia): ?>
                        <option value="<?=$dia;?>"><?=$dia;?> </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Hora Inicio</label>
                        <input type="time" name="hora_inicio" class="form-control" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Hora Fin</label>
                        <input type="time" name="hora_fin" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar Clase</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
