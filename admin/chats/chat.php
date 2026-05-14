<?php
$id_destinatario = $_GET['id'];
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

$id_destinatario = intval($_GET['id'] ?? 0);
if ($id_destinatario <= 0 || !isChatContactAllowed($pdo, $id_usuario_sesion, $rol_sesion_usuario, $id_destinatario)) {
    header('Location: index.php');
    exit();
}

// Marcar mensajes como leídos
$query_leidos = $pdo->prepare("UPDATE mensajes_chat SET leido = 1 WHERE remitente_id = :id_destinatario AND destinatario_id = :id_usuario");
$query_leidos->bindParam(':id_destinatario', $id_destinatario);
$query_leidos->bindParam(':id_usuario', $id_usuario_sesion);
$query_leidos->execute();

// Obtener datos del destinatario
$query_dest = $pdo->prepare("SELECT u.email, r.nombre_rol, p.nombres, p.apellidos 
    FROM usuarios u
    INNER JOIN roles r ON u.rol_id = r.id_rol
    LEFT JOIN personas p ON u.id_usuario = p.usuario_id
    WHERE u.id_usuario = :id_destinatario");
$query_dest->bindParam(':id_destinatario', $id_destinatario);
$query_dest->execute();
$destinatario = $query_dest->fetch(PDO::FETCH_ASSOC);
$nombre_destinatario = getDisplayNameFromDatos($destinatario);

// Obtener historial de mensajes
$query_mensajes = $pdo->prepare("SELECT * FROM mensajes_chat 
    WHERE (remitente_id = :id_usuario AND destinatario_id = :id_destinatario)
    OR (remitente_id = :id_destinatario AND destinatario_id = :id_usuario)
    ORDER BY fyh_creacion ASC");
$query_mensajes->bindParam(':id_usuario', $id_usuario_sesion);
$query_mensajes->bindParam(':id_destinatario', $id_destinatario);
$query_mensajes->execute();
$mensajes = $query_mensajes->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- DIRECT CHAT -->
                    <div class="card direct-chat direct-chat-info shadow">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title">Chat con <?=$nombre_destinatario;?> (<?=$destinatario['nombre_rol'];?>)</h3>
                            <div class="card-tools">
                                <a href="index.php" class="btn btn-tool text-white"><i class="fas fa-times"></i> Volver</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Conversations are loaded here -->
                            <div class="direct-chat-messages" style="height: 400px;">
                                <?php if(empty($mensajes)): ?>
                                    <div class="text-center text-muted mt-5">No hay mensajes. ¡Inicia la conversación!</div>
                                <?php endif; ?>
                                <?php foreach($mensajes as $msg): 
                                    $es_mio = ($msg['remitente_id'] == $id_usuario_sesion);
                                    $fecha = date('d/m/Y H:i', strtotime($msg['fyh_creacion']));
                                ?>
                                    <!-- Message -->
                                    <div class="direct-chat-msg <?=$es_mio ? 'right' : '';?>">
                                        <div class="direct-chat-infos clearfix">
                                            <span class="direct-chat-name float-<?=$es_mio ? 'right' : 'left';?>">
                                                <?=$es_mio ? 'Tú' : $nombre_destinatario;?>
                                            </span>
                                            <span class="direct-chat-timestamp float-<?=$es_mio ? 'left' : 'right';?>"><?=$fecha;?></span>
                                        </div>
                                        <!-- /.direct-chat-infos -->
                                        <img class="direct-chat-img" src="https://cdn-icons-png.flaticon.com/512/6073/6073873.png" alt="message user image">
                                        <!-- /.direct-chat-img -->
                                        <div class="direct-chat-text">
                                            <?=htmlspecialchars($msg['mensaje']);?>
                                        </div>
                                        <!-- /.direct-chat-text -->
                                    </div>
                                    <!-- /.direct-chat-msg -->
                                <?php endforeach; ?>
                            </div>
                            <!--/.direct-chat-messages-->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <form action="<?=APP_URL;?>/app/controllers/chats/send.php" method="post">
                                <input type="hidden" name="destinatario_id" value="<?=$id_destinatario;?>">
                                <div class="input-group">
                                    <input type="text" name="mensaje" placeholder="Escribe un mensaje..." class="form-control" required autocomplete="off" autofocus>
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-info">Enviar</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-footer-->
                    </div>
                    <!--/.direct-chat -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll to bottom
    $(document).ready(function(){
        $('.direct-chat-messages').scrollTop($('.direct-chat-messages')[0].scrollHeight);
    });
</script>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
