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
$query_est_datos = $pdo->prepare("SELECT per.nombres, per.apellidos, est.grado_id FROM estudiantes est INNER JOIN personas per ON est.persona_id = per.id_persona WHERE est.id_estudiante = '$id_estudiante'");
$query_est_datos->execute();
$datos_estudiante = $query_est_datos->fetch(PDO::FETCH_ASSOC);
$nombre_estudiante = $datos_estudiante ? $datos_estudiante['nombres'].' '.$datos_estudiante['apellidos'] : 'Estudiante No Asignado';
$id_grado_estudiante = $datos_estudiante ? $datos_estudiante['grado_id'] : 0;

// Obtener todas las materias asignadas al grado del estudiante y sus calificaciones
$sql_calificaciones = "SELECT DISTINCT m.id_materia, m.nombre_materia, c.nota1, c.nota2, c.nota3 
    FROM asignaciones a
    INNER JOIN materias m ON a.materia_id = m.id_materia
    LEFT JOIN calificaciones c ON c.materia_id = m.id_materia AND c.estudiante_id = '$id_estudiante'
    WHERE a.grado_id = '$id_grado_estudiante' AND a.estado = '1'";
$query_cal = $pdo->prepare($sql_calificaciones);
$query_cal->execute();
$calificaciones_est = $query_cal->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info shadow" style="border-radius: 15px;">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold" style="color: #17a2b8;">
                                <i class="bi bi-file-earmark-text"></i> Boletín de Calificaciones
                            </h3>
                            <div>
                                <button onclick="window.print()" class="btn btn-sm btn-info" style="border-radius: 20px;"><i class="bi bi-printer"></i> Imprimir Boletín</button>
                            </div>
                        </div>
                        <div class="card-body printable-area">
                            <div class="text-center mb-4">
                                <h2 class="d-none d-print-block">Boletín de Calificaciones</h2>
                                <h4>Alumno(a): <b><?=$nombre_estudiante;?></b></h4>
                            </div>
                            
                            <?php if(empty($calificaciones_est)): ?>
                                <div class="alert alert-warning text-center"><i class="bi bi-exclamation-triangle"></i> El estudiante aún no tiene calificaciones registradas.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center align-middle">
                                        <thead class="bg-info text-white">
                                            <tr>
                                                <th>Materia</th>
                                                <th>1er Trimestre</th>
                                                <th>2do Trimestre</th>
                                                <th>3er Trimestre</th>
                                                <th>Promedio Final</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($calificaciones_est as $nota): 
                                                $n1 = is_numeric($nota['nota1']) ? $nota['nota1'] : 0;
                                                $n2 = is_numeric($nota['nota2']) ? $nota['nota2'] : 0;
                                                $n3 = is_numeric($nota['nota3']) ? $nota['nota3'] : 0;
                                                $promedio = ($n1 + $n2 + $n3) / 3;
                                                
                                                if($nota['nota1'] === null && $nota['nota2'] === null && $nota['nota3'] === null) {
                                                    $promedio_format = '-';
                                                    $estado_color = 'text-muted';
                                                    $estado_texto = 'Sin Calificar';
                                                } else {
                                                    $promedio_format = number_format($promedio, 2);
                                                    $estado_color = $promedio >= 3.5 ? 'text-success font-weight-bold' : 'text-danger font-weight-bold';
                                                    $estado_texto = $promedio >= 3.5 ? 'Aprobado' : 'Reprobado';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-left font-weight-bold"><?=$nota['nombre_materia'];?></td>
                                                <td><?=$nota['nota1'] ?: '-';?></td>
                                                <td><?=$nota['nota2'] ?: '-';?></td>
                                                <td><?=$nota['nota3'] ?: '-';?></td>
                                                <td class="bg-light"><h5><?=$promedio_format;?></h5></td>
                                                <td class="<?=$estado_color;?>"><?=$estado_texto;?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .printable-area, .printable-area * { visibility: visible; }
    .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
    .card-header, .main-sidebar, .main-header { display: none !important; }
}
</style>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
