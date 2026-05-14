<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 28/11/2025
 * Time: 19:18
 */
define('SERVIDOR','localhost');
define('USUARIO','root');
define('PASSWORD','12345');
define('BD','sisgestionescolar');

define('APP_NAME','SISTEMA DE GESTIÓN ESCOLAR');
define('APP_URL','http://localhost/sisgestionescolar');
define('KEY_API_MAPS','');

$servidor = "mysql:dbname=".BD.";host=".SERVIDOR;

try{
    $pdo = new PDO($servidor,USUARIO,PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "conexión existosa a la base de datos";
}catch (PDOException $e){
    //print_r($e);
    echo "Error: No se pudo conectar a la base de datos. Verifique sus credenciales y que el servidor MySQL esté activo.";
    echo "<br>Detalle del error: " . $e->getMessage();
}

date_default_timezone_set("America/Caracas");
$fechaHora = date('Y-m-d H:i:s');

$fecha_actual = date('Y-m-d');
$dia_actual = date('d');
$mes_actual = date('m');
$ano_actual = date('Y');

$estado_de_registro = '1';



