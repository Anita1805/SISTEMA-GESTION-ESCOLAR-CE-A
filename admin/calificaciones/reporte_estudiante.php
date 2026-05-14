<?php

$id_estudiante_get = $_GET['id_estudiante'];

include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

include ('../../app/controllers/estudiantes/listado_de_estudiantes.php');
include ('../../app/controllers/calificaciones/listado_de_calificaciones.php');



$curso ="";
$paralelo = "";
foreach ($estudiantes as $estudiante){

    if($id_estudiante_get == $estudiante['id_estudiante']){
        $nombres = $estudiante['nombres'];
        $apellidos = $estudiante['apellidos'];
        $curso = $estudiante['curso'];
        $paralelo = $estudiante['paralelo'];
    }
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="row">
                <h2>Calificaciones del estudiante: <?=$apellidos." ".$nombres;?></h2>
            </div>
            <br>
            <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Calificaiones registrados</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th><center>Nro</center></th>
                                    <th><center>Materia</center></th>
                                    <th><center>1er trimestre</center></th>
                                    <th><center>2do trimestre</center></th>
                                    <th><center>3er trimestre</center></th>
                                    <th><center>Promedio Final</center></th>
                                    <th><center>Estado</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_calificaciones = 0;
                                $nota1 = "";
                                $nota2 = "";
                                $nota3 = "";
                                foreach ($calificaciones as $calificacione){
                                    if($id_estudiante_get == $calificacione['estudiante_id']){
                                        $contador_calificaciones = $contador_calificaciones +1;
                                        $nota1 = $calificacione['nota1'];
                                        $nota2 = $calificacione['nota2'];
                                        $nota3 = $calificacione['nota3'];
                                        $promedio = null;
                                        $estado_text = 'Sin Calificar';
                                        $estado_class = 'text-muted';
                                        if(is_numeric($nota1) && is_numeric($nota2) && is_numeric($nota3)){
                                            $promedio = number_format((floatval($nota1) + floatval($nota2) + floatval($nota3)) / 3, 2);
                                            if($promedio >= 3.5){
                                                $estado_text = 'Aprobado';
                                                $estado_class = 'text-success font-weight-bold';
                                            } else {
                                                $estado_text = 'Reprobado';
                                                $estado_class = 'text-danger font-weight-bold';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><center><?=$contador_calificaciones;?></center></td>
                                            <td><center><?=$calificacione['nombre_materia'];?></center></td>
                                            <td style="text-align: center"><?=$nota1 ?? '-';?></td>
                                            <td style="text-align: center"><?=$nota2 ?? '-';?></td>
                                            <td style="text-align: center"><?=$nota3 ?? '-';?></td>
                                            <td style="text-align: center"><?= $promedio ?? '-';?></td>
                                            <td class="<?=$estado_class;?>"><center><?=$estado_text;?></center></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');

?>

<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 5,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Calificaciones",
                "infoEmpty": "Mostrando 0 a 0 de 0 Calificaciones",
                "infoFiltered": "(Filtrado de _MAX_ total Calificaciones)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Calificaciones",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [{
                    text: 'Copiar',
                    extend: 'copy',
                }, {
                    extend: 'pdf'
                },{
                    extend: 'csv'
                },{
                    extend: 'excel'
                },{
                    text: 'Imprimir',
                    extend: 'print'
                }
                ]
            },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas',
                    collectionLayout: 'fixed three-column'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
