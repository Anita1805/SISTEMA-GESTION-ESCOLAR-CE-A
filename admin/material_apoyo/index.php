<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

if($rol_sesion_usuario == "ESTUDIANTE"){
    include('vista_estudiante.php');
    exit;
}

// Obtener el ID del usuario logueado para filtrar su propio material (o ver todo si es admin)
$id_usuario_actual = null;
$email_sesion = $_SESSION['sesion_email'];
$query = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = '$email_sesion'");
$query->execute();
$user_data = $query->fetch(PDO::FETCH_ASSOC);
if($user_data){
    $id_usuario_actual = $user_data['id_usuario'];
}

// Obtener el material
$sql_material = "SELECT mat.*, usu.email FROM material_apoyo as mat INNER JOIN usuarios as usu ON mat.usuario_id = usu.id_usuario WHERE mat.estado = '1'";
// Si solo queremos que cada docente vea el suyo (opcional, por ahora lo dejamos ver todo)
// $sql_material .= " AND mat.usuario_id = '$id_usuario_actual'";

$query_material = $pdo->prepare($sql_material);
$query_material->execute();
$materiales = $query_material->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1>Material de Apoyo Audiovisual</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Material</a>
                </div>
            </div>

            <div class="row">
                <?php
                if(empty($materiales)){
                    echo '<div class="col-12"><div class="alert alert-info">Aún no hay material audiovisual subido. ¡Sé el primero en agregar uno!</div></div>';
                }
                foreach ($materiales as $material) {
                    $enlace = $material['enlace_video'];
                    // Intentar extraer el ID de YouTube para poner miniatura si es posible
                    $yt_id = '';
                    if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $enlace, $match)) {
                        $yt_id = $match[1];
                    }
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <?php if($yt_id != ''){ ?>
                                <img src="https://img.youtube.com/vi/<?=$yt_id?>/hqdefault.jpg" class="card-img-top" alt="Miniatura Video" style="height: 200px; object-fit: cover;">
                            <?php } else { ?>
                                <div class="bg-dark text-white d-flex justify-content-center align-items-center" style="height: 200px;">
                                    <i class="bi bi-camera-video" style="font-size: 4rem;"></i>
                                </div>
                            <?php } ?>
                            
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold" style="color: #b21f1f;"><?=$material['titulo'];?></h5>
                                <p class="card-text text-muted mt-2" style="font-size: 0.9rem; height: 60px; overflow: hidden;"><?=$material['descripcion'];?></p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <?php if(!empty($material['enlace_video'])){ ?>
                                        <a href="<?=$material['enlace_video'];?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-play-circle"></i> Ver Video</a>
                                    <?php } ?>
                                    
                                    <small class="text-muted text-right w-100">Por: <?=$material['email'];?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
