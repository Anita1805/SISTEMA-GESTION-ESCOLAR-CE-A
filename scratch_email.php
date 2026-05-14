<?php
session_start();
$_SESSION['sesion_email'] = 'docente1@admin.com'; // Use Teacher 4 or Teacher 1's email. Wait, what is Teacher 4's email?
// Let's get Teacher 4's email
include ('app/config.php');
$q = $pdo->query("SELECT u.email FROM usuarios u INNER JOIN personas p ON p.usuario_id=u.id_usuario INNER JOIN docentes d ON d.persona_id=p.id_persona WHERE d.id_docente=4");
$email = $q->fetchColumn();
echo "Email: $email\n";
