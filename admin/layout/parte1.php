<?php
session_start();

if(isset($_SESSION['sesion_email'])){
   // echo "el usuarios paso por el login";
    $email_sesion = $_SESSION['sesion_email'];
    $query_sesion = $pdo->prepare("SELECT * FROM usuarios as usu
                                    INNER JOIN roles as rol ON rol.id_rol = usu.rol_id 
                                    LEFT JOIN personas as per ON per.usuario_id = usu.id_usuario
                                    WHERE usu.email = :email AND usu.estado = '1' ");
    $query_sesion->bindParam(':email', $email_sesion);
    $query_sesion->execute();

    $datos_sesion_usuarios = $query_sesion->fetchAll(PDO::FETCH_ASSOC);

    if(empty($datos_sesion_usuarios)){
        // If user data is missing or person record not found, redirect to login
        session_destroy();
        header('Location:'.APP_URL."/login");
        exit();
    }

    foreach ($datos_sesion_usuarios as $datos_sesion_usuario){
       $nombre_sesion_usuario = $datos_sesion_usuario['email'];
       $id_rol_sesion_usuario = $datos_sesion_usuario['id_rol'];
       $rol_sesion_usuario = $datos_sesion_usuario['nombre_rol'];
       $id_usuario_sesion = $datos_sesion_usuario['id_usuario'];
       $nombres_sesion_usuario = $datos_sesion_usuario['nombres'] ?? 'Usuario';
       $apellidos_sesion_usuario = $datos_sesion_usuario['apellidos'] ?? '';
       $ci_sesion_usuario = $datos_sesion_usuario['ci'] ?? '';

       if($rol_sesion_usuario == 'ADMINISTRADOR'){
           $nombre_sesion_usuario = 'Ana Sofia Vega';
           $nombres_sesion_usuario = 'Ana Sofia';
           $apellidos_sesion_usuario = 'Vega';
       }
    }

    function getDisplayNameFromDatos($datos) {
        if(!empty($datos['nombre_rol']) && $datos['nombre_rol'] === 'ADMINISTRADOR'){
            return 'Ana Sofia Vega';
        }
        if(!empty($datos['nombres']) && !empty($datos['apellidos']) && $datos['nombres'] !== $datos['email']){
            return $datos['nombres'].' '.$datos['apellidos'];
        }
        if(!empty($datos['nombres']) && $datos['nombres'] !== $datos['email']){
            return $datos['nombres'];
        }
        return $datos['email'];
    }

    function getAllowedChatContacts($pdo, $id_usuario_sesion, $rol_sesion_usuario) {
        switch ($rol_sesion_usuario) {
            case 'ADMINISTRADOR':
                $sql = "SELECT u.id_usuario, u.email, r.nombre_rol, p.nombres, p.apellidos
                        FROM usuarios u
                        INNER JOIN roles r ON u.rol_id = r.id_rol
                        LEFT JOIN personas p ON u.id_usuario = p.usuario_id
                        WHERE u.estado = '1' AND u.id_usuario != :id_usuario
                        ORDER BY r.nombre_rol ASC, p.apellidos ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'DOCENTE':
                $sql = "SELECT DISTINCT u.id_usuario, u.email, r.nombre_rol, p.nombres, p.apellidos
                        FROM usuarios u
                        INNER JOIN roles r ON u.rol_id = r.id_rol
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN estudiantes est ON est.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.nivel_id = est.nivel_id AND a.grado_id = est.grado_id
                        INNER JOIN docentes doc ON doc.id_docente = a.docente_id
                        INNER JOIN personas per_doc ON per_doc.id_persona = doc.persona_id
                        WHERE u.estado = '1' AND u.id_usuario != :id_usuario AND per_doc.usuario_id = :id_usuario
                        ORDER BY p.apellidos ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'ESTUDIANTE':
                $sql = "SELECT DISTINCT u.id_usuario, u.email, r.nombre_rol, p.nombres, p.apellidos
                        FROM usuarios u
                        INNER JOIN roles r ON u.rol_id = r.id_rol
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN docentes d ON d.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.docente_id = d.id_docente
                        INNER JOIN estudiantes est_ses ON est_ses.nivel_id = a.nivel_id AND est_ses.grado_id = a.grado_id
                        INNER JOIN personas p_ses ON p_ses.id_persona = est_ses.persona_id
                        WHERE u.estado = '1' AND u.id_usuario != :id_usuario AND p_ses.usuario_id = :id_usuario
                        ORDER BY p.apellidos ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'PADRE DE FAMILIA':
                $sql = "SELECT DISTINCT u.id_usuario, u.email, r.nombre_rol, p.nombres, p.apellidos
                        FROM usuarios u
                        INNER JOIN roles r ON u.rol_id = r.id_rol
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN docentes d ON d.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.docente_id = d.id_docente
                        INNER JOIN estudiantes est ON est.nivel_id = a.nivel_id AND est.grado_id = a.grado_id
                        INNER JOIN ppffs pf ON pf.estudiante_id = est.id_estudiante
                        WHERE u.estado = '1' AND u.id_usuario != :id_usuario AND pf.usuario_id = :id_usuario
                        ORDER BY p.apellidos ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            default:
                return [];
        }
    }

    function isChatContactAllowed($pdo, $id_usuario_sesion, $rol_sesion_usuario, $destinatario_id) {
        if ($destinatario_id === $id_usuario_sesion) {
            return false;
        }

        switch ($rol_sesion_usuario) {
            case 'ADMINISTRADOR':
                $sql = "SELECT 1 FROM usuarios u WHERE u.estado = '1' AND u.id_usuario = :destinatario_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':destinatario_id', $destinatario_id);
                $stmt->execute();
                return (bool) $stmt->fetchColumn();
            case 'DOCENTE':
                $sql = "SELECT 1
                        FROM usuarios u
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN estudiantes est ON est.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.nivel_id = est.nivel_id AND a.grado_id = est.grado_id
                        INNER JOIN docentes doc ON doc.id_docente = a.docente_id
                        INNER JOIN personas per_doc ON per_doc.id_persona = doc.persona_id
                        WHERE u.estado = '1' AND u.id_usuario = :destinatario_id AND per_doc.usuario_id = :id_usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':destinatario_id', $destinatario_id);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return (bool) $stmt->fetchColumn();
            case 'ESTUDIANTE':
                $sql = "SELECT 1
                        FROM usuarios u
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN docentes d ON d.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.docente_id = d.id_docente
                        INNER JOIN estudiantes est_ses ON est_ses.nivel_id = a.nivel_id AND est_ses.grado_id = a.grado_id
                        INNER JOIN personas p_ses ON p_ses.id_persona = est_ses.persona_id
                        WHERE u.estado = '1' AND u.id_usuario = :destinatario_id AND p_ses.usuario_id = :id_usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':destinatario_id', $destinatario_id);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return (bool) $stmt->fetchColumn();
            case 'PADRE DE FAMILIA':
                $sql = "SELECT 1
                        FROM usuarios u
                        INNER JOIN personas p ON u.id_usuario = p.usuario_id
                        INNER JOIN docentes d ON d.persona_id = p.id_persona
                        INNER JOIN asignaciones a ON a.docente_id = d.id_docente
                        INNER JOIN estudiantes est ON est.nivel_id = a.nivel_id AND est.grado_id = a.grado_id
                        INNER JOIN ppffs pf ON pf.estudiante_id = est.id_estudiante
                        WHERE u.estado = '1' AND u.id_usuario = :destinatario_id AND pf.usuario_id = :id_usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':destinatario_id', $destinatario_id);
                $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                $stmt->execute();
                return (bool) $stmt->fetchColumn();
            default:
                return false;
        }
    }

    $url = $_SERVER["PHP_SELF"];
    // Improved path extraction: get everything after the project folder
    $base_path = parse_url(APP_URL, PHP_URL_PATH);
    $rest = str_replace($base_path, '', $url);


    try {
        $sql_roles_permisos = "SELECT * FROM roles_permisos as rolper 
                           INNER JOIN permisos as per ON per.id_permiso = rolper.permiso_id 
                           INNER JOIN roles as rol ON rol.id_rol = rolper.rol_id
                           where rolper.estado = '1' ";
        $query_roles_permisos = $pdo->prepare($sql_roles_permisos);
        $query_roles_permisos->execute();
        $roles_permisos = $query_roles_permisos->fetchAll(PDO::FETCH_ASSOC);
        $contadorpermiso = 0;
        foreach ($roles_permisos as $roles_permiso){
            if($id_rol_sesion_usuario == $roles_permiso['rol_id']){
                
                if($rest == $roles_permiso['url']){
                   
                    $contadorpermiso = $contadorpermiso + 1;
                }
            }
        }
    } catch (PDOException $e) {
        // If table doesn't exist, we bypass the check to allow access
        $contadorpermiso = 1;
    }

    // Lista de rutas que siempre deben permitirse temporalmente hasta registrarlas en BD
    $rutas_nuevas_permitidas = [
        '/admin/horarios',
        '/admin/horarios/index.php',
        '/admin/horarios/gestionar.php',
        '/admin/horarios/vista_estudiante.php',
        '/admin/horarios/vista_docente.php',
        '/admin/chats',
        '/admin/chats/index.php',
        '/admin/chats/chat.php',
        '/app/controllers/chats/send.php',
        '/admin/material_apoyo',
        '/admin/material_apoyo/create.php',
        '/admin/material_apoyo/vista_estudiante.php',
        '/admin/kardex',
        '/admin/kardex/index.php',
        '/admin/calificaciones',
        '/admin/calificaciones/create.php',
        '/admin/calificaciones/vista_estudiante.php',
        '/admin/calificaciones/vista_padre.php',
        '/admin/estado_cuenta/paz_y_salvo.php',
        '/admin/pagos',
        '/admin/notificaciones',
        '/admin/observador',
        '/admin/observador/index.php',
        '/admin/observador/vista_padre.php',
        '/admin/observador/vista_estudiante.php'
    ];

    $es_ruta_nueva = false;
    foreach ($rutas_nuevas_permitidas as $r_nueva) {
        if (strpos($rest, $r_nueva) === 0) {
            $es_ruta_nueva = true;
            break;
        }
    }

    if($contadorpermiso > 0 || $es_ruta_nueva || $rol_sesion_usuario == "ADMINISTRADOR"){
        //echo "ruta autorizada";
    }else{
        header('Location:'.APP_URL."/admin/no-autorizado.php");
        exit();
    }




}else{
    // echo "el usuario no paso por el login";
    header('Location:'.APP_URL."/login");
    exit();
}

if (defined('SKIP_LAYOUT') && SKIP_LAYOUT) {
    return;
}
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_NAME;?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/dist/css/adminlte.min.css">

    <!-- jQuery -->
    <script src="<?=APP_URL;?>/public/plugins/jquery/jquery.min.js"></script>

    <!-- Sweetaler2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Iconos de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Datatables -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- CHART -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/css/dashboard_custom.css">

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?=APP_URL;?>/admin" class="nav-link">Hola, <?=$nombre_sesion_usuario;?></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <?php
            // Obtener notificaciones del usuario logueado
            $query_notificaciones = $pdo->prepare("SELECT * FROM notificaciones WHERE usuario_id = :usuario_id ORDER BY fyh_creacion DESC LIMIT 5");
            $query_notificaciones->bindParam(':usuario_id', $id_usuario_sesion);
            $query_notificaciones->execute();
            $notificaciones = $query_notificaciones->fetchAll(PDO::FETCH_ASSOC);

            // Contar no leídas
            $query_no_leidas = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :usuario_id AND leida = 0");
            $query_no_leidas->bindParam(':usuario_id', $id_usuario_sesion);
            $query_no_leidas->execute();
            $cant_no_leidas = $query_no_leidas->fetchColumn();
            ?>
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <?php if($cant_no_leidas > 0): ?>
                        <span class="badge badge-danger navbar-badge" style="font-size: 0.7rem;"><?=$cant_no_leidas;?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width: 300px;">
                    <span class="dropdown-header font-weight-bold bg-light"><?=$cant_no_leidas;?> Notificaciones Nuevas</span>
                    <div class="dropdown-divider"></div>
                    
                    <?php if(empty($notificaciones)): ?>
                        <a href="#" class="dropdown-item text-center text-muted py-3">No tienes notificaciones</a>
                    <?php else: ?>
                        <?php foreach($notificaciones as $noti): 
                            // Marcar con color diferente si no está leída
                            $bg_color = ($noti['leida'] == 0) ? 'bg-white' : 'bg-light';
                            $text_weight = ($noti['leida'] == 0) ? 'font-weight-bold text-dark' : 'text-muted';
                            
                            // Formatear tiempo (simple)
                            $fecha_corta = date('d/m H:i', strtotime($noti['fyh_creacion']));
                        ?>
                        <a href="<?=APP_URL;?>/app/controllers/notificaciones/leer.php?id=<?=$noti['id_notificacion'];?>" class="dropdown-item <?=$bg_color;?>" style="white-space: normal; line-height: 1.2; padding: 10px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bell mr-2 text-primary"></i> 
                                <div class="flex-grow-1">
                                    <span class="<?=$text_weight;?> d-block" style="font-size: 0.9rem;"><?=$noti['titulo'];?></span>
                                    <span class="text-muted" style="font-size: 0.8rem;"><?=$noti['mensaje'];?></span>
                                </div>
                                <span class="float-right text-muted text-xs ml-2"><?=$fecha_corta;?></span>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <a href="<?=APP_URL;?>/admin/notificaciones" class="dropdown-item dropdown-footer text-primary font-weight-bold">Ver todas las notificaciones</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?=APP_URL;?>/admin" class="brand-link">
            <img src="<?=APP_URL;?>/public/images/logo_real.png" alt="Logo" class="brand-image">
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://cdn-icons-png.flaticon.com/512/6073/6073873.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?=$nombre_sesion_usuario;?></a>
                </div>
            </div>


            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="DIRECTOR ACADÉMICO") || ($rol_sesion_usuario=="DIRECTOR ADMINISTRATIVO")){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-gear"></i></i>
                                <p>
                                    Configuraciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/configuraciones" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Configurar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php
                    }
                    ?>


                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="DIRECTOR ACADÉMICO") ){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-bookshelf"></i></i>
                                <p>
                                    Niveles
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/niveles" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de niveles</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-bar-chart-steps"></i></i>
                                <p>
                                    Grados
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/grados" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de grados</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-book-half"></i></i>
                                <p>
                                    Materias
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/materias" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de materias</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>




                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") ){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-bookmarks"></i></i>
                                <p>
                                    Roles
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/roles" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de roles</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/roles/permisos.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de Permisos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-people-fill"></i></i>
                                <p>
                                    Usuarios
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/usuarios" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de usuarios</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>





                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="DIRECTOR ACADÉMICO") || ($rol_sesion_usuario=="DIRECTOR ADMINISTRATIVO") || ($rol_sesion_usuario=="SECRETARIA")){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-person-lines-fill"></i></i>
                                <p>
                                    Administrativos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/administrativos" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de administrativos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>



                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="DIRECTOR ACADÉMICO") || ($rol_sesion_usuario=="SECRETARIA")){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-person-video3"></i></i>
                                <p>
                                    Docentes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/docentes" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de docentes</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/docentes/asignacion.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asignación de materias</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>




                    <?php
                    // MENU DOCENTE
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="DOCENTE") ){ ?>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/kardex" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-clipboard-check"></i></i>
                                <p>Kardex</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/calificaciones" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-check2-square"></i></i>
                                <p>Calificaciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/material_apoyo" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-play-btn"></i></i>
                                <p>Asignaciones y Material</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/observador" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-eye"></i></i>
                                <p>Observador del Alumno</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/horarios" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-calendar-week"></i></i>
                                <p><?=($rol_sesion_usuario=="ADMINISTRADOR") ? "Gestión de Horarios" : "Mi Horario";?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/chats" class="nav-link active" style="background-color: #17a2b8;">
                                <i class="nav-icon fas"><i class="bi bi-chat-dots"></i></i>
                                <p>Mensajes / Chat</p>
                            </a>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    // MENU ESTUDIANTE
                    if($rol_sesion_usuario=="ESTUDIANTE"){ ?>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/calificaciones" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-check2-square"></i></i>
                                <p>Mis Calificaciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/observador" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-eye"></i></i>
                                <p>Mi Observador</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/material_apoyo" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-play-btn"></i></i>
                                <p>Mis Asignaciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/horarios" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-calendar-week"></i></i>
                                <p>Mi Horario</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/chats" class="nav-link active" style="background-color: #17a2b8;">
                                <i class="nav-icon fas"><i class="bi bi-chat-dots"></i></i>
                                <p>Mis Profesores (Chat)</p>
                            </a>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    // MENU PADRE DE FAMILIA
                    if($rol_sesion_usuario=="PADRE DE FAMILIA"){ ?>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/calificaciones" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-check2-square"></i></i>
                                <p>Calificaciones y Boletín</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/observador" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-eye"></i></i>
                                <p>Observador del Alumno</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/horarios" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-calendar-week"></i></i>
                                <p>Horario Escolar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=APP_URL;?>/admin/estado_cuenta" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-cash-coin"></i></i>
                                <p>Estado de Cuenta</p>
                            </a>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    // MENU SECRETARIA / ADMINISTRATIVOS / ADMIN
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") || ($rol_sesion_usuario=="SECRETARIA") ){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-person-video"></i></i>
                                <p>
                                    Estudiantes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/inscripciones" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inscripción</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/estudiantes" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de estudiantes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    if( ($rol_sesion_usuario=="ADMINISTRADOR") ){ ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas"><i class="bi bi-cash-coin"></i></i>
                                <p>
                                    Estado de Cuenta
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?=APP_URL;?>/admin/pagos" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Imprimir Paz y Salvo</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>

                    <li class="nav-item">
                        <a href="<?=APP_URL;?>/login/logout.php" class="nav-link" style="background-color: #eb2d14;color: black">
                            <i class="nav-icon fas"><i class="bi bi-door-open"></i></i>
                            <p>
                                Cerrar sesión
                            </p>
                        </a>
                    </li>


                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
