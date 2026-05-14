<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

if($rol_sesion_usuario == "PADRE DE FAMILIA"){
    include('vista_padre.php');
    exit;
}
if($rol_sesion_usuario == "ESTUDIANTE"){
    include('vista_estudiante.php');
    exit;
}

// Lógica para el DOCENTE
$email_sesion = $_SESSION['sesion_email'];
// Obtener docente_id
$query = $pdo->prepare("SELECT d.id_docente FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN docentes as d ON d.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query->execute();
$data_doc = $query->fetch(PDO::FETCH_ASSOC);
$id_docente = $data_doc ? $data_doc['id_docente'] : 0;

// Obtener estudiantes asignados a este docente
$sql_estudiantes = "SELECT DISTINCT est.id_estudiante, per.nombres, per.apellidos, g.curso, g.paralelo 
    FROM asignaciones a
    INNER JOIN estudiantes est ON a.grado_id = est.grado_id
    INNER JOIN personas per ON est.persona_id = per.id_persona
    INNER JOIN grados g ON est.grado_id = g.id_grado
    WHERE a.docente_id = '$id_docente'";
$query_est = $pdo->prepare($sql_estudiantes);
$query_est->execute();
$estudiantes = $query_est->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #0056b3;">
                                <i class="bi bi-eye"></i> Observador del Alumno
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if(empty($estudiantes)): ?>
                                <div class="alert alert-info">No tienes estudiantes asignados.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table id="tabla_observador" class="table table-hover table-bordered table-striped text-center align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nro</th>
                                                <th>Nombres y Apellidos</th>
                                                <th>Grado y Paralelo</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $contador = 0;
                                            foreach($estudiantes as $estudiante): 
                                                $contador++;
                                            ?>
                                            <tr>
                                                <td><?=$contador;?></td>
                                                <td class="text-left font-weight-bold"><?=$estudiante['apellidos'].' '.$estudiante['nombres'];?></td>
                                                <td><?=$estudiante['curso'].' "'.$estudiante['paralelo'].'"';?></td>
                                                <td>
                                                    <a href="create.php?id_estudiante=<?=$estudiante['id_estudiante'];?>" class="btn btn-sm btn-primary" style="border-radius: 20px;">
                                                        <i class="bi bi-pencil-square"></i> Registrar / Ver Observaciones
                                                    </a>
                                                </td>
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

<script>
    $(function () {
        $("#tabla_observador").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "responsive": true, "lengthChange": false, "autoWidth": false,
        });
    });
</script>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
