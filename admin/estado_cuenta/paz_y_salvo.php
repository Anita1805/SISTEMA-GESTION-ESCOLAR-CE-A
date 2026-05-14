<?php
$id_estudiante = $_GET['id_estudiante'];

include ('../../app/config.php');
require_once('../../public/TCPDF-main/tcpdf.php');

include ('../../app/controllers/configuraciones/institucion/listado_de_instituciones.php');
include ('../../app/controllers/estudiantes/datos_del_estudiante.php');

// Obtener datos de la institución
foreach ($instituciones as $institucione){
    $nombre_institucion = $institucione['nombre_institucion'];
    $direccion = $institucione['direccion'];
    $telefono = $institucione['telefono'];
    $celular = $institucione['celular'];
    $correo = $institucione['correo'];
    $logo = $institucione['logo'];
}

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor(APP_NAME);
$pdf->setTitle('Certificado de Paz y Salvo');
$pdf->setSubject('Certificado');
$pdf->setKeywords('Paz y Salvo, Escolar');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(15, 15, 15);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setFont('Times', '', 12);
$pdf->AddPage();

$style = array(
    'border' => 0,
    'vpadding' => '3',
    'hpadding' => '3',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, 
    'module_width' => 1, 
    'module_height' => 1 
);

$codigo_verificacion = uniqid();
$QR = 'Certificado de Paz y Salvo emitido por '.$nombre_institucion.' a favor de '.$apellidos.' '.$nombres.' CI: '.$ci.' con fecha '.$fechaHora.' COD: '.$codigo_verificacion;
$pdf->write2DBarcode($QR,'QRCODE,L',  160,20,30,30, $style);

// Set some content to print
$html = '
<table border="0">
<tr>
    <td width="150px" style="text-align: center"><img src="../../public/images/configuracion/'.$logo.'" width="80px" alt=""></td>
    <td width="400px" style="text-align: center">
        <h3><b>'.$nombre_institucion.'</b></h3>
        <p><small>'.$direccion.'<br>Tel: '.$telefono.' Cel: '.$celular.'</small></p>
    </td>
    <td width="100px"></td>
</tr>
</table>
<br><br><br>
<h2 style="text-align: center;"><u>CERTIFICADO DE PAZ Y SALVO</u></h2>
<br><br>
<p style="text-align: justify; line-height: 1.5;">
A quien corresponda:
<br><br>
Por medio del presente documento, la Administración y Dirección Académica de la Unidad Educativa <b>'.$nombre_institucion.'</b>, hace constar que el/la estudiante:
</p>

<table border="0" cellpadding="5">
<tr>
    <td width="150px"><b>Nombre Completo:</b></td>
    <td>'.$apellidos.' '.$nombres.'</td>
</tr>
<tr>
    <td width="150px"><b>C.I.:</b></td>
    <td>'.$ci.'</td>
</tr>
<tr>
    <td width="150px"><b>Nivel:</b></td>
    <td>'.$nivel.' - '.$turno.'</td>
</tr>
<tr>
    <td width="150px"><b>Grado/Curso:</b></td>
    <td>'.$curso.' | Paralelo: '.$paralelo.'</td>
</tr>
</table>

<p style="text-align: justify; line-height: 1.5;">
<br><br>
Se encuentra <b>AL DÍA (PAZ Y SALVO)</b> con sus obligaciones económicas y administrativas con la institución hasta la fecha de emisión de este certificado.
<br><br>
Para los fines que convengan al interesado(a), se expide el presente certificado a los '.$dia_actual.' días del mes de '.$mes_actual.' del año '.$ano_actual.'.
</p>

<br><br><br><br><br><br>

<table border="0">
<tr>
    <td style="text-align: center">
     _______________________________________ <br>
     <b>Firma de Administración/Caja</b> <br>
    </td>
    <td style="text-align: center">
    ________________________________________ <br>
    <b>Firma Dirección</b> <br>
    </td>
</tr>
</table>
<br><br>
<p style="font-size: 10px; color: #555;"><i>Documento generado automáticamente por el sistema '.APP_NAME.'. Código de verificación: '.$codigo_verificacion.'</i></p>
';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

$pdf->Output('paz_y_salvo_'.$ci.'.pdf', 'I');
?>
