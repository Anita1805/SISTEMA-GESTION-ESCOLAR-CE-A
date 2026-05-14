<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

if($rol_sesion_usuario == "ESTUDIANTE" || $rol_sesion_usuario == "PADRE DE FAMILIA"){
    include('vista_estudiante.php');
    exit;
}
if($rol_sesion_usuario == "DOCENTE"){
    include('vista_docente.php');
    exit;
}

// Lógica para ADMINISTRADOR
include ('../../app/controllers/grados/listado_de_grados.php');
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
                                <i class="bi bi-calendar3"></i> Gestión de Horarios Escolares
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_grados" class="table table-hover table-bordered table-striped text-center align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Nivel</th>
                                            <th>Curso</th>
                                            <th>Paralelo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $contador = 0;
                                        foreach($grados as $grado): 
                                            $contador++;
                                        ?>
                                        <tr>
                                            <td><?=$contador;?></td>
                                            <td><?=$grado['nivel'];?></td>
                                            <td><?=$grado['curso'];?></td>
                                            <td><?=$grado['paralelo'];?></td>
                                            <td>
                                                <a href="gestionar.php?id_grado=<?=$grado['id_grado'];?>" class="btn btn-sm btn-primary" style="border-radius: 20px;">
                                                    <i class="bi bi-calendar-plus"></i> Armar / Ver Horario
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#tabla_grados").DataTable({
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
