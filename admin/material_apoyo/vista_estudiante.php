<?php
if(!isset($_SESSION['sesion_email']) || !isset($pdo)) {
    header("Location: index.php");
    exit;
}
// Obtener el ID del estudiante logueado
$email_sesion = $_SESSION['sesion_email'];
$query_est = $pdo->prepare("SELECT est.id_estudiante, est.grado_id FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN estudiantes as est ON est.persona_id = per.id_persona
    WHERE usu.email = :email");
$query_est->execute(['email' => $email_sesion]);
$data_est = $query_est->fetch(PDO::FETCH_ASSOC);

$materiales_por_materia = [];

if($data_est){
    $grado_id = $data_est['grado_id'];
    
    // Buscar las asignaciones (materias y docentes) para el grado del estudiante
    $sql_asignaciones = "SELECT a.materia_id, m.nombre_materia
                         FROM asignaciones a 
                         INNER JOIN materias m ON a.materia_id = m.id_materia
                         WHERE a.grado_id = :grado_id AND a.estado = '1'";
    $query_asign = $pdo->prepare($sql_asignaciones);
    $query_asign->execute(['grado_id' => $grado_id]);
    $asignaciones = $query_asign->fetchAll(PDO::FETCH_ASSOC);

    foreach($asignaciones as $asign) {
        $materia_id = $asign['materia_id'];
        $materia_nombre = $asign['nombre_materia'];
        
        // Obtener materiales de esa materia
        $sql_mat = "SELECT mat.*, u.email as prof_email 
                    FROM material_apoyo mat 
                    INNER JOIN usuarios u ON mat.usuario_id = u.id_usuario 
                    WHERE mat.materia_id = :materia_id AND mat.estado = '1'";
        $query_mat = $pdo->prepare($sql_mat);
        $query_mat->execute(['materia_id' => $materia_id]);
        $mats = $query_mat->fetchAll(PDO::FETCH_ASSOC);
        
        // Siempre inicializar la materia para que aparezca su cuadro
        if(!isset($materiales_por_materia[$materia_nombre])) {
            $materiales_por_materia[$materia_nombre] = [];
        }
        
        if(!empty($mats)){
            $materiales_por_materia[$materia_nombre] = array_merge($materiales_por_materia[$materia_nombre], $mats);
        }
    }
}
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <h1 class="font-weight-bold" style="color: #b21f1f;"><i class="bi bi-book"></i> Mis Asignaciones y Material</h1>
                    <p class="text-muted">Aquí encontrarás videos, talleres y material de apoyo organizado por materias.</p>
                </div>
            </div>

            <?php
            if(empty($materiales_por_materia)){
                echo '<div class="alert alert-info shadow-sm"><i class="bi bi-info-circle"></i> Aún no hay asignaciones o material subido por tus profesores.</div>';
            }

            foreach ($materiales_por_materia as $materia_nombre => $materiales) {
                ?>
                <div class="card mb-4 shadow-sm" style="border-radius: 15px; border-left: 5px solid #b21f1f;">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-dark font-weight-bold"><i class="bi bi-bookmark-fill" style="color:#b21f1f;"></i> Materia: <?=$materia_nombre;?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if(empty($materiales)) {
                                echo '<div class="col-12"><p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Aún no hay material asignado para esta materia.</p></div>';
                            } else {
                                foreach ($materiales as $material) {
                                    $enlace = $material['enlace_video'];
                                    $yt_id = '';
                                    if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $enlace, $match)) {
                                        $yt_id = $match[1];
                                    }
                                    ?>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card h-100 shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #ddd;">
                                            <?php if($yt_id != ''){ ?>
                                                <img src="https://img.youtube.com/vi/<?=$yt_id?>/hqdefault.jpg" class="card-img-top" alt="Miniatura Video" style="height: 180px; object-fit: cover;">
                                            <?php } else { ?>
                                                <div class="bg-light text-secondary d-flex justify-content-center align-items-center" style="height: 180px;">
                                                    <i class="bi bi-file-earmark-play" style="font-size: 3rem;"></i>
                                                </div>
                                            <?php } ?>
                                            
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title font-weight-bold text-dark" style="width: 100%;"><?=$material['titulo'];?></h5>
                                                <p class="card-text text-muted mt-2 flex-grow-1" style="font-size: 0.85rem;"><?=$material['descripcion'];?></p>
                                                
                                                <div class="mt-3 pt-2 border-top">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <?php if(!empty($material['enlace_video'])){ ?>
                                                            <a href="<?=$material['enlace_video'];?>" target="_blank" class="btn btn-sm text-white" style="background-color: #b21f1f; border-radius: 20px;"><i class="bi bi-play-circle"></i> Ver</a>
                                                        <?php } ?>
                                                        <?php if(!empty($material['archivo_pdf'])){ ?>
                                                            <a href="<?=APP_URL;?>/public/uploads/materiales/<?=$material['archivo_pdf'];?>" download class="btn btn-sm btn-info" style="border-radius: 20px;"><i class="bi bi-download"></i> Bajar</a>
                                                        <?php } ?>
                                                    </div>
                                                    <small class="text-muted text-center d-block"><i class="bi bi-person"></i> Prof. <?=$material['prof_email'];?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
