<?php

$id_grado_get = $_GET['id_grado'];
$id_docente_get = $_GET['id_docente'];
$id_materia_get = $_GET['id_materia'];

include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

include ('../../app/controllers/estudiantes/listado_de_estudiantes.php');
include ('../../app/controllers/calificaciones/listado_de_calificaciones.php');



$curso ="";
$paralelo = "";
foreach ($estudiantes as $estudiante){

    if($id_grado_get == $estudiante['id_grado']){
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
                <h2>Listado de estudiantes del grado: <?=$curso;?></h2>
            </div>
            <br>
            <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Estudiantes registrados</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th><center>Nro</center></th>
                                    <th><center>Apellidos y nombres</center></th>
                                    <th><center>Nivel</center></th>
                                    <th><center>Turno</center></th>
                                    <th><center>Grado</center></th>
                                    <th><center>1er Trimestre</center></th>
                                    <th><center>2do Trimestre</center></th>
                                    <th><center>3er Trimestre</center></th>
                                    <th><center>Promedio</center></th>
                                    <th><center>Estado</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_estudiantes = 0;

                                foreach ($estudiantes as $estudiante){
                                    if($id_grado_get == $estudiante['id_grado']){
                                        $id_estudiante = $estudiante['id_estudiante'];
                                        $contador_estudiantes = $contador_estudiantes +1; ?>
                                        <tr>
                                            <td style="text-align: center">
                                                <input type="text" id="estudiante_<?=$contador_estudiantes;?>"
                                                       value="<?=$id_estudiante;?>" hidden>
                                                <?=$contador_estudiantes;?>
                                            </td>
                                            <td><?=$estudiante['apellidos']." ".$estudiante['nombres'];?></td>
                                            <td style="text-align: center"><?=$estudiante['nivel'];?></td>
                                            <td style="text-align: center"><?=$estudiante['turno'];?></td>
                                            <td style="text-align: center"><?=$estudiante['curso'];?></td>
                                            <?php
                                            $nota1 = "";
                                            $nota2 = "";
                                            $nota3 = "";
                                            foreach ($calificaciones as $calificacione){
                                                if( ($calificacione['docente_id']==$id_docente_get)
                                                 && ($calificacione['estudiante_id']==$id_estudiante)
                                                 && ($calificacione['materia_id']==$id_materia_get)
                                                ){
                                                    $nota1 = $calificacione['nota1'];
                                                    $nota2 = $calificacione['nota2'];
                                                    $nota3 = $calificacione['nota3'];
                                                }
                                            }

                                            $promedio = null;
                                            $estado_text = '-';
                                            $estado_class = 'text-muted';
                                            if($nota1 !== "" && $nota2 !== "" && $nota3 !== ""){
                                                $promedio_val = (float)$nota1 + (float)$nota2 + (float)$nota3;
                                                $promedio = number_format($promedio_val / 3, 2);
                                                if($promedio >= 3.5){
                                                    $estado_text = 'Aprobado';
                                                    $estado_class = 'text-success font-weight-bold';
                                                } else {
                                                    $estado_text = 'Reprobado';
                                                    $estado_class = 'text-danger font-weight-bold';
                                                }
                                            }
                                            ?>
                                            <td>
                                                <input style="text-align:center" value="<?=$nota1;?>" id="nota1_<?=$contador_estudiantes;?>" type="number" step="0.1" min="0" max="5.0" class="form-control">
                                            </td>
                                            <td>
                                                <input style="text-align:center" value="<?=$nota2;?>" id="nota2_<?=$contador_estudiantes;?>" type="number" step="0.1" min="0" max="5.0" class="form-control">
                                            </td>
                                            <td>
                                                <input style="text-align:center" value="<?=$nota3;?>" id="nota3_<?=$contador_estudiantes;?>" type="number" step="0.1" min="0" max="5.0" class="form-control">
                                            </td>
                                            <td><center><?= $promedio ?? '-'; ?></center></td>
                                            <td class="<?=$estado_class;?>"><center><?=$estado_text;?></center></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                $contador_estudiantes= $contador_estudiantes;
                                ?>
                                </tbody>
                            </table>
                            <hr>
                            <button class="btn btn-primary btn-lg" id="btn_guardar">Guardar notas</button>
                            <div id="respuesta" hidden></div>
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
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Estudiantes",
                "infoEmpty": "Mostrando 0 a 0 de 0 Estudiantes",
                "infoFiltered": "(Filtrado de _MAX_ total Estudiantes)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Estudiantes",
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

    $('#btn_guardar').click(function () {
        var n = '<?=$contador_estudiantes;?>';
        var i = 1;
        var id_docente = '<?=$id_docente_get;?>';
        var id_materia = '<?=$id_materia_get;?>';

        for (i = 1; i<=n ;i++){

            var a = '#nota1_'+i;
            var nota1 = $(a).val();

            var b = '#nota2_'+i;
            var nota2 = $(b).val();

            var c = '#nota3_'+i;
            var nota3 = $(c).val();

            var d = '#estudiante_'+i;
            var id_estudiante = $(d).val();

            var url = "../../app/controllers/calificaciones/create.php";
            $.get(url,{id_docente:id_docente,id_estudiante:id_estudiante,id_materia:id_materia,nota1:nota1,nota2:nota2,nota3:nota3},function (datos) {
                $('#respuesta').html(datos);
            });
        }
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Se actualizaron las notas correctamente",
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
