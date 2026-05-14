<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Obtener los usuarios de chat permitidos según el rol y la asignación
$usuarios = getAllowedChatContacts($pdo, $id_usuario_sesion, $rol_sesion_usuario);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info shadow" style="border-radius: 15px;">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold" style="color: #17a2b8;">
                                <i class="bi bi-chat-dots"></i> Directorio de Contactos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_contactos" class="table table-hover table-striped align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Rol</th>
                                            <th>Nombre Completo</th>
                                            <th>Email</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $contador = 0;
                                        foreach($usuarios as $user): 
                                            $contador++;
                                            $nombre_completo = getDisplayNameFromDatos($user);
                                        ?>
                                        <tr>
                                            <td><?=$contador;?></td>
                                            <td><span class="badge badge-secondary"><?=$user['nombre_rol'];?></span></td>
                                            <td><?=$nombre_completo;?></td>
                                            <td><?=$user['email'];?></td>
                                            <td>
                                                <a href="chat.php?id=<?=$user['id_usuario'];?>" class="btn btn-sm btn-info" style="border-radius: 20px;">
                                                    <i class="bi bi-chat-text"></i> Enviar Mensaje
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#tabla_contactos").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "responsive": true, "lengthChange": false, "autoWidth": false,
        });
    });
</script>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>
