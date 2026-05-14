<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 17/1/2024
 * Time: 08:14
 */

include ('../../../app/config.php');

$rol_id = $_POST['rol_id'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$ci = $_POST['ci'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$celular = $_POST['celular'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
$nivel_id = $_POST['nivel_id'];
$grado_id = $_POST['grado_id'];
$rude = $_POST['rude'];
$nombres_apellidos_ppff = $_POST['nombres_apellidos_ppff'];
$email_ppff = $_POST['email_ppff'];
$ci_ppf = $_POST['ci_ppf'];
$celular_ppff = $_POST['celular_ppff'];
$ocupacion_ppff = $_POST['ocupacion_ppff'];
$ref_nombre = $_POST['ref_nombre'];
$ref_parentezco = $_POST['ref_parentezco'];
$ref_celular = $_POST['ref_celular'];
$profesion = "ESTUDIANTE";

$pdo->beginTransaction();

///////////////////////// INSERTAR A LA TABLA USUARIOS
$password = password_hash($ci, PASSWORD_DEFAULT);

$sentencia = $pdo->prepare('INSERT INTO usuarios
         (rol_id, email, password, fyh_creacion, estado)
VALUES ( :rol_id,:email,:password,:fyh_creacion,:estado)');

$sentencia->bindParam(':rol_id',$rol_id);
$sentencia->bindParam(':email',$email);
$sentencia->bindParam(':password',$password);
$sentencia->bindParam('fyh_creacion',$fechaHora);
$sentencia->bindParam('estado',$estado_de_registro);
$sentencia->execute();

$id_usuario = $pdo->lastInsertId();


//////////////////////// INSERTAR A LA TABLA PERSONAS
$sentencia = $pdo->prepare('INSERT INTO personas
         (usuario_id,nombres,apellidos,ci,fecha_nacimiento,celular,profesion,direccion, fyh_creacion, estado)
VALUES ( :usuario_id,:nombres,:apellidos,:ci,:fecha_nacimiento,:celular,:profesion,:direccion,:fyh_creacion,:estado)');

$sentencia->bindParam(':usuario_id',$id_usuario);
$sentencia->bindParam(':nombres',$nombres);
$sentencia->bindParam(':apellidos',$apellidos);
$sentencia->bindParam(':ci',$ci);
$sentencia->bindParam(':fecha_nacimiento',$fecha_nacimiento);
$sentencia->bindParam(':celular',$celular);
$sentencia->bindParam(':profesion',$profesion);
$sentencia->bindParam(':direccion',$direccion);
$sentencia->bindParam('fyh_creacion',$fechaHora);
$sentencia->bindParam('estado',$estado_de_registro);
$sentencia->execute();

$id_persona = $pdo->lastInsertId();

/////////////////////// INSERTAR A LA TABLA ESTUDIANTES
$sentencia = $pdo->prepare('INSERT INTO estudiantes
         (persona_id, nivel_id, grado_id, rude, fyh_creacion, estado)
VALUES ( :persona_id,:nivel_id,:grado_id,:rude,:fyh_creacion,:estado)');

$sentencia->bindParam(':persona_id',$id_persona);
$sentencia->bindParam(':nivel_id',$nivel_id);
$sentencia->bindParam(':grado_id',$grado_id);
$sentencia->bindParam('rude',$rude);
$sentencia->bindParam('fyh_creacion',$fechaHora);
$sentencia->bindParam('estado',$estado_de_registro);
$sentencia->execute();

$id_estudiante = $pdo->lastInsertId();


/////////////////////// CREAR USUARIO PARA PADRE DE FAMILIA
$rol_ppff_id = 8; // ID rol PADRE DE FAMILIA
$password_ppff = password_hash($ci_ppf, PASSWORD_DEFAULT);

$sentencia_usu_p = $pdo->prepare('INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) VALUES (:rol_id,:email,:password,:fyh_creacion,:estado)');
$sentencia_usu_p->execute(['rol_id'=>$rol_ppff_id, 'email'=>$email_ppff, 'password'=>$password_ppff, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
$id_usuario_ppff = $pdo->lastInsertId();

/////////////////////// CREAR PERSONA PARA PADRE DE FAMILIA
$nombres_ap_split = explode(" ", $nombres_apellidos_ppff);
$nombres_p = $nombres_ap_split[0] ?? '';
$apellidos_p = isset($nombres_ap_split[1]) ? implode(" ", array_slice($nombres_ap_split, 1)) : '';
$profesion_p = $ocupacion_ppff;
$fecha_nacimiento_p = '1900-01-01'; // Default required by database
$direccion_p = ''; // Default required by database

$sentencia_per_p = $pdo->prepare('INSERT INTO personas (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, profesion, direccion, fyh_creacion, estado) VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, :profesion, :direccion, :fyh_creacion, :estado)');
$sentencia_per_p->execute(['usuario_id'=>$id_usuario_ppff, 'nombres'=>$nombres_p, 'apellidos'=>$apellidos_p, 'ci'=>$ci_ppf, 'fecha_nacimiento'=>$fecha_nacimiento_p, 'celular'=>$celular_ppff, 'profesion'=>$profesion_p, 'direccion'=>$direccion_p, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);


/////////////////////// INSERTAR A LA TABLA PPFFS
$sentencia = $pdo->prepare('INSERT INTO ppffs
         (estudiante_id , usuario_id, nombres_apellidos_ppff, ci_ppf, celular_ppff, ocupacion_ppff, ref_nombre, ref_parentezco, ref_celular, fyh_creacion, estado)
VALUES ( :estudiante_id , :usuario_id, :nombres_apellidos_ppff,:ci_ppf,:celular_ppff,:ocupacion_ppff,:ref_nombre,:ref_parentezco,:ref_celular,:fyh_creacion,:estado)');

$sentencia->bindParam(':estudiante_id',$id_estudiante);
$sentencia->bindParam(':usuario_id',$id_usuario_ppff);
$sentencia->bindParam(':nombres_apellidos_ppff',$nombres_apellidos_ppff);
$sentencia->bindParam(':ci_ppf',$ci_ppf);
$sentencia->bindParam(':celular_ppff',$celular_ppff);
$sentencia->bindParam(':ocupacion_ppff',$ocupacion_ppff);
$sentencia->bindParam(':ref_nombre',$ref_nombre);
$sentencia->bindParam(':ref_parentezco',$ref_parentezco);
$sentencia->bindParam(':ref_celular',$ref_celular);
$sentencia->bindParam('fyh_creacion',$fechaHora);
$sentencia->bindParam('estado',$estado_de_registro);

if($sentencia->execute()){
    echo 'success';
    $pdo->commit();
    session_start();
    $_SESSION['mensaje'] = "Se registro al estudiante de la manera correcta en la base de datos";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/estudiantes");
//header('Location:' .$URL.'/');
}else{
    echo 'error al registrar a la base de datos';
    $pdo->rollBack();
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base datos, comuniquese con el administrador";
    $_SESSION['icono'] = "error";
    ?><script>window.history.back();</script><?php
}
