<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

if($rol_sesion_usuario == "ESTUDIANTE"){
    include('vista_estudiante.php');
    exit;
}
if($rol_sesion_usuario == "PADRE DE FAMILIA"){
    include('vista_padre.php');
    exit;
}

include ('../../app/controllers/docentes/listado_de_asignaciones.php');

$asignaciones_mostrar = [];
foreach ($asignaciones as $asignacione) {
    if($rol_sesion_usuario == "ADMINISTRADOR" || $email_sesion == $asignacione['email']){
        $asignaciones_mostrar[] = $asignacione;
    }
}

if($rol_sesion_usuario == "ADMINISTRADOR"){
    $sql_todas_calificaciones = "SELECT cal.*, m.nombre_materia, per.nombres AS docente_nombres, per.apellidos AS docente_apellidos, per_est.nombres AS estudiante_nombres, per_est.apellidos AS estudiante_apellidos, niv.nivel, gra.curso, gra.paralelo, niv.turno
        FROM calificaciones cal
        INNER JOIN materias m ON m.id_materia = cal.materia_id
        INNER JOIN docentes doc ON doc.id_docente = cal.docente_id
        INNER JOIN personas per ON per.id_persona = doc.persona_id
        INNER JOIN estudiantes est ON est.id_estudiante = cal.estudiante_id
        INNER JOIN personas per_est ON per_est.id_persona = est.persona_id
        INNER JOIN grados gra ON gra.id_grado = est.grado_id
        INNER JOIN niveles niv ON niv.id_nivel = gra.nivel_id
        WHERE cal.estado = '1'";
    $query_todas_calificaciones = $pdo->prepare($sql_todas_calificaciones);
    $query_todas_calificaciones->execute();
    $todas_calificaciones = $query_todas_calificaciones->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Grados asignados para calificaciones</h1>
            </div>
            <br>
            <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Asignaciones registradas</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th><center>Nro</center></th>
                                    <th><center>Docente</center></th>
                                    <th><center>Nivel</center></th>
                                    <th><center>Turno</center></th>
                                    <th><center>Grado</center></th>
                                    <th><center>Materia</center></th>
                                    <th><center>Acciones</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador = 0;
                                foreach ($asignaciones_mostrar as $asignacione){
                                    $id_grado = $asignacione['id_grado'];
                                    $contador++;
                                    $docente_nombre = $asignacione['nombres'] . ' ' . $asignacione['apellidos']; ?>
                                    <tr>
                                        <td><center><?=$contador;?></center></td>
                                        <td><center><?=$docente_nombre;?></center></td>
                                        <td><center><?=$asignacione['nivel'];?></center></td>
                                        <td><center><?=$asignacione['turno'];?></center></td>
                                        <td><center><?=$asignacione['curso'];?></center></td>
                                        <td><center><?=$asignacione['nombre_materia'];?></center></td>
                                        <td style="text-align: center">
                                            <a href="create.php?id_grado=<?=$id_grado;?>&&id_docente=<?=$asignacione['docente_id'];?>&&id_materia=<?=$asignacione['materia_id'];?>"
                                               class="btn btn-primary btn-sm"><i class="bi bi-check2-square"></i> Subir Notas</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                if(empty($asignaciones_mostrar)){
                                    echo '<tr><td colspan="7" class="text-center">No hay asignaciones disponibles para este usuario.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <?php if($rol_sesion_usuario == "ADMINISTRADOR"): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">Todas las calificaciones registradas</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th><center>Nro</center></th>
                                    <th><center>Estudiante</center></th>
                                    <th><center>Materia</center></th>
                                    <th><center>Docente</center></th>
                                    <th><center>Nivel</center></th>
                                    <th><center>Grado</center></th>
                                    <th><center>1er Trimestre</center></th>
                                    <th><center>2do Trimestre</center></th>
                                    <th><center>3er Trimestre</center></th>
                                    <th><center>Promedio</center></th>
                                    <th><center>Estado</center></th>
                                    <th><center>Acción</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_cal = 0;
                                foreach ($todas_calificaciones as $fila) {
                                    $contador_cal++;
                                    $nota1 = is_numeric($fila['nota1']) ? (float)$fila['nota1'] : null;
                                    $nota2 = is_numeric($fila['nota2']) ? (float)$fila['nota2'] : null;
                                    $nota3 = is_numeric($fila['nota3']) ? (float)$fila['nota3'] : null;
                                    $promedio = null;
                                    $estado_texto = 'Sin calificar';
                                    $estado_color = 'text-muted';
                                    if($nota1 !== null && $nota2 !== null && $nota3 !== null){
                                        $promedio = number_format(($nota1 + $nota2 + $nota3) / 3, 2);
                                        if($promedio >= 3.5){
                                            $estado_texto = 'Aprobado';
                                            $estado_color = 'text-success font-weight-bold';
                                        } else {
                                            $estado_texto = 'Reprobado';
                                            $estado_color = 'text-danger font-weight-bold';
                                        }
                                    } else {
                                        $promedio = '-';
                                    }
                                    $estudiante_nombre = $fila['estudiante_apellidos'] . ' ' . $fila['estudiante_nombres'];
                                    $docente_nombre = $fila['docente_apellidos'] . ' ' . $fila['docente_nombres'];
                                    $curso_grado = $fila['curso'] . ' ' . $fila['paralelo'];
                                ?>
                                    <tr>
                                        <td><center><?=$contador_cal;?></center></td>
                                        <td><center><?=$estudiante_nombre;?></center></td>
                                        <td><center><?=$fila['nombre_materia'];?></center></td>
                                        <td><center><?=$docente_nombre;?></center></td>
                                        <td><center><?=$fila['nivel'];?></center></td>
                                        <td><center><?=$curso_grado;?></center></td>
                                        <td><center><?=$nota1 ?? '-';?></center></td>
                                        <td><center><?=$nota2 ?? '-';?></center></td>
                                        <td><center><?=$nota3 ?? '-';?></center></td>
                                        <td><center><?=$promedio;?></center></td>
                                        <td class="<?=$estado_color;?>"><center><?=$estado_texto;?></center></td>
                                        <td><center>
                                            <a href="reporte_estudiante.php?id_estudiante=<?=$fila['id_estudiante'];?>" class="btn btn-info btn-sm">Ver</a>
                                        </center></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');

?>
