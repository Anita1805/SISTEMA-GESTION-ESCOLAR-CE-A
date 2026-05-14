<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Obtener materias asignadas al docente
$email_sesion = $_SESSION['sesion_email'];
$query_docente = $pdo->prepare("SELECT id_docente FROM docentes d INNER JOIN personas p ON d.persona_id = p.id_persona INNER JOIN usuarios u ON p.usuario_id = u.id_usuario WHERE u.email = :email");
$query_docente->execute(['email' => $email_sesion]);
$data_doc = $query_docente->fetch(PDO::FETCH_ASSOC);

$materias_docente = [];
if($data_doc){
    $id_docente = $data_doc['id_docente'];
    $sql_mat = "SELECT DISTINCT m.id_materia, m.nombre_materia FROM asignaciones a INNER JOIN materias m ON a.materia_id = m.id_materia WHERE a.docente_id = :id_docente AND a.estado = '1'";
    $query_mat = $pdo->prepare($sql_mat);
    $query_mat->execute(['id_docente' => $id_docente]);
    $materias_docente = $query_mat->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card card-outline card-primary shadow" style="border-radius: 15px;">
                        <div class="card-header" style="border-bottom: 1px solid #eee;">
                            <h3 class="card-title font-weight-bold" style="color: #b21f1f;">Agregar Nuevo Material Audiovisual</h3>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?=APP_URL;?>/app/controllers/material_apoyo/create.php" method="post" enctype="multipart/form-data">
                                
                                <div class="form-group mb-4">
                                    <label for="materia_id">Materia <span class="text-danger">*</span></label>
                                    <select name="materia_id" class="form-control" required style="border-radius: 8px;">
                                        <option value="">Seleccione una materia...</option>
                                        <?php foreach($materias_docente as $materia): ?>
                                            <option value="<?=$materia['id_materia'];?>"><?=$materia['nombre_materia'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="titulo">Título del Material <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" class="form-control" placeholder="Ej: Clase de Matemáticas - Ecuaciones" required style="border-radius: 8px;">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Breve resumen del contenido..." style="border-radius: 8px;"></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="enlace_video">Enlace del Video (YouTube / Google Drive) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        </div>
                                        <input type="url" name="enlace_video" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                                    </div>
                                    <small class="form-text text-muted">Asegúrate de que el enlace sea público o accesible para los estudiantes.</small>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="archivo">Subir Archivo (Opcional)</label>
                                    <input type="file" name="archivo" class="form-control" style="border-radius: 8px;">
                                </div>
                                <hr>
                                
                                <div class="row mt-4">
                                    <div class="col-md-12 text-right">
                                        <a href="index.php" class="btn btn-secondary mr-2" style="border-radius: 20px; padding: 8px 20px;">Cancelar</a>
                                        <button type="submit" class="btn btn-primary" style="background-color: #b21f1f; border: none; border-radius: 20px; padding: 8px 30px;">
                                            <i class="bi bi-cloud-arrow-up"></i> Guardar Material
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
