<?php
include ('../../../app/config.php');

session_start();
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$enlace_video = $_POST['enlace_video'];
$materia_id = $_POST['materia_id'];

// Necesitamos el ID de usuario del docente logueado
$email_sesion = $_SESSION['sesion_email'];
$query = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = '$email_sesion'");
$query->execute();
$user_data = $query->fetch(PDO::FETCH_ASSOC);

if($user_data){
    $usuario_id = $user_data['id_usuario'];

    $nombre_archivo = "";
    if(isset($_FILES['archivo']) && $_FILES['archivo']['name'] != ''){
        $nombre_archivo = date('Y-m-d-H-i-s')."-".$_FILES['archivo']['name'];
        $ruta = "../../../public/uploads/materiales/".$nombre_archivo;
        move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);
    }

    $sentencia = $pdo->prepare('INSERT INTO material_apoyo
    (usuario_id, materia_id, titulo, descripcion, enlace_video, archivo_pdf, fyh_creacion, estado)
    VALUES ( :usuario_id, :materia_id, :titulo, :descripcion, :enlace_video, :archivo_pdf, :fyh_creacion, :estado)');

    $sentencia->bindParam(':usuario_id', $usuario_id);
    $sentencia->bindParam(':materia_id', $materia_id);
    $sentencia->bindParam(':titulo', $titulo);
    $sentencia->bindParam(':descripcion', $descripcion);
    $sentencia->bindParam(':enlace_video', $enlace_video);
    $sentencia->bindParam(':archivo_pdf', $nombre_archivo);
    $sentencia->bindParam('fyh_creacion', $fechaHora);
    $estado_del_registro = '1';
    $sentencia->bindParam('estado', $estado_del_registro);

    if($sentencia->execute()){
        // Notificar a todos los estudiantes asignados a este docente
        // 1. Obtener id_docente
        $q_doc = $pdo->prepare("SELECT id_docente FROM docentes d INNER JOIN personas p ON d.persona_id = p.id_persona WHERE p.usuario_id = :uid");
        $q_doc->execute(['uid' => $usuario_id]);
        $doc = $q_doc->fetch(PDO::FETCH_ASSOC);
        if($doc){
            $id_docente = $doc['id_docente'];
            // 2. Obtener los usuario_id de los estudiantes en los grados asignados al docente
            $sql_est = "SELECT DISTINCT u.id_usuario FROM usuarios u 
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN estudiantes e ON e.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.grado_id = e.grado_id
                        WHERE a.docente_id = :doc_id AND u.estado = '1'";
            $q_est = $pdo->prepare($sql_est);
            $q_est->execute(['doc_id' => $id_docente]);
            $estudiantes_notificar = $q_est->fetchAll(PDO::FETCH_ASSOC);

            // 3. Insertar notificacion
            $titulo_noti = "Nuevo Material de Apoyo";
            $mensaje_noti = "El profesor ha subido un nuevo material: ".$titulo;
            $enlace_noti = APP_URL."/admin/material_apoyo";

            $sent_noti = $pdo->prepare("INSERT INTO notificaciones (usuario_id, titulo, mensaje, leida, enlace, estado, fyh_creacion) VALUES (:usuario_id, :titulo, :mensaje, 0, :enlace, '1', :fyh)");
            
            foreach($estudiantes_notificar as $est_n){
                $sent_noti->execute([
                    'usuario_id' => $est_n['id_usuario'],
                    'titulo' => $titulo_noti,
                    'mensaje' => $mensaje_noti,
                    'enlace' => $enlace_noti,
                    'fyh' => $fechaHora
                ]);
            }
        }

        $_SESSION['mensaje'] = "El material audiovisual fue registrado correctamente.";
        $_SESSION['icono'] = "success";
        header('Location:'.APP_URL."/admin/material_apoyo");
    }else{
        $_SESSION['mensaje'] = "Error, no se pudo registrar el material.";
        $_SESSION['icono'] = "error";
        header('Location:'.APP_URL."/admin/material_apoyo/create.php");
    }
} else {
    $_SESSION['mensaje'] = "Error de sesión. No se encontró su perfil de usuario.";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/material_apoyo/create.php");
}
?>
