<?php
// Obtener el ID del estudiante logueado
$email_sesion = $_SESSION['sesion_email'];
$query_est = $pdo->prepare("SELECT est.id_estudiante, est.grado_id FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN estudiantes as est ON est.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query_est->execute();
$data_est = $query_est->fetch(PDO::FETCH_ASSOC);
$id_estudiante = $data_est ? $data_est['id_estudiante'] : 0;
$id_grado_estudiante = $data_est ? $data_est['grado_id'] : 0;

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
                    <div class="card card-outline card-success shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #28a745;">
                                <i class="bi bi-award"></i> Mi Boletín de Calificaciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if(empty($calificaciones_est)): ?>
                                <div class="alert alert-info">Aún no tienes calificaciones registradas en el sistema.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered text-center align-middle">
                                        <thead class="bg-light">
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
<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
