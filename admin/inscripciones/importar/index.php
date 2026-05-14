<?php

include ('../../../app/config.php');
include ('../../../admin/layout/parte1.php');

?>

<script src="xlsx.js"></script>
<script language="JavaScript">
    var oFileIn;
    //Código JQuery
    $(function() {
        oFileIn = document.getElementById('my_file_input');
        if (oFileIn.addEventListener) {
            oFileIn.addEventListener('change', filePicked, false);
        }
    });

    //Método que hace el proceso de importar excel a html
    function filePicked(oEvent) {
        // Obtener el archivo del input
        var oFile = oEvent.target.files[0];
        var sFilename = oFile.name;
        // Crear un Archivo de Lectura HTML5
        var reader = new FileReader();

        // Leyendo los eventos cuando el archivo ha sido seleccionado
        reader.onload = function(e) {
            var data = e.target.result;
            var cfb = XLS.CFB.read(data, {
                type: 'binary'
            });
            var wb = XLS.parse_xlscfb(cfb);
            // Iterando sobre cada sheet
            wb.SheetNames.forEach(function(sheetName) {
                // Obtener la fila actual como CSV
                var sCSV = XLS.utils.make_csv(wb.Sheets[sheetName]);
                var data = XLS.utils.sheet_to_json(wb.Sheets[sheetName], {
                    header: 1
                });
                $.each(data, function(indexR, valueR) {
                    var sRow = "<tr>";
                    $.each(data[indexR], function(indexC, valueC) {
                        sRow = sRow + "<td>" + valueC + "</td>";
                    });
                    sRow = sRow + "</tr>";
                    $("#my_file_output").append(sRow);
                });
            });
            $("#imgImport"). css("display", "none");
        };


// Llamar al JS Para empezar a leer el archivo .. Se podría retrasar esto si se desea
        reader.readAsBinaryString(oFile);
    }
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Importar estudiantes</h1>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos de los estudiantes</h3>
                            <div class="card-tools">
                                <a href="PLANTILLA_IMPORTAR_ESTUDIANTES_a.xls" class="btn btn-success"><i class="bi bi-cloud-download-fill"></i> Descargar plantilla</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="file" id="my_file_input" class="form-control" />
                            <div id="imgImport">
                                <br>
                                <center>

                                </center>
                            </div>
                            <br>
                            <div class="table table-responsive">
                                <table id='my_file_output' border=""
                                       class="table table-bordered table-condensed table-striped"></table>
                            </div>
                            <button id="btn_lectura" class="btn btn-info">Registrar estudiantes</button>
                            <a href="" class="btn btn-default ">Cancelar</a>
                            <p id="respuesta">

                            </p>
                            <p id="contador">

                            </p>
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

<script>
    $('#btn_lectura').click(function () {
        valores=new Array();
        var contador = 0;
        $('#my_file_output tr').each(function () {

            var d1 = $(this).find('td').eq(0).html();
            var d2 = $(this).find('td').eq(1).html();
            var d3 = $(this).find('td').eq(2).html();
            var d4 = $(this).find('td').eq(3).html();
            var d5 = $(this).find('td').eq(4).html();
            var d6 = $(this).find('td').eq(5).html();
            var d7 = $(this).find('td').eq(6).html();
            var d8 = $(this).find('td').eq(7).html();
            var d9 = $(this).find('td').eq(8).html();
            var d10 = $(this).find('td').eq(9).html();
            var d11 = $(this).find('td').eq(10).html();
            var d12 = $(this).find('td').eq(11).html();
            var d13 = $(this).find('td').eq(12).html();
            var d14 = $(this).find('td').eq(13).html();
            var d15 = $(this).find('td').eq(14).html();
            var d16 = $(this).find('td').eq(15).html();
            var d17 = $(this).find('td').eq(16).html();
            var d18 = $(this).find('td').eq(17).html();

            valor=new Array(d1, d2, d3, d4, d5, d6, d7,d8,d9,d10,d11,d12,d13,d14,d15,d16,d17,d18);
            valores.push(valor);
            console.log (valor);
             //alert(valor);
           $.post('insertar.php', {d1:d1, d2:d2, d3:d3, d4:d4, d5:d5, d6:d6, d7:d7, d8:d8, d9:d9, d10:d10,d11:d11, d12:d12, d13:d13, d14:d14, d15:d15, d16:d16, d17:d17, d18:d18 }, function (datos) {
                $('#respuesta').html(datos);
               contador = contador + 1;
               $('#contador').html("Se registro "+contador+" registros correctamente.");
           });



        });
        // alert("hola");
    });
</script>

<?php

include ('../../../admin/layout/parte2.php');
include ('../../../layout/mensajes.php');

?>

