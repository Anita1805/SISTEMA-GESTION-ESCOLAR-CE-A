<?php
session_start();
$_SESSION['sesion_email'] = 'DocenteVanegas@escuela.com';
$_SERVER["PHP_SELF"] = '/sisgestionescolar/admin/observador/index.php';

// Simulate index.php
include('app/config.php');

$email_sesion = $_SESSION['sesion_email'];
// Obtener docente_id
$query = $pdo->prepare("SELECT d.id_docente FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN docentes as d ON d.persona_id = per.id_persona
    WHERE usu.email = '$email_sesion'");
$query->execute();
$data_doc = $query->fetch(PDO::FETCH_ASSOC);
$id_docente = $data_doc ? $data_doc['id_docente'] : 0;

// Obtener estudiantes asignados a este docente
$sql_estudiantes = "SELECT DISTINCT est.id_estudiante, per.nombres, per.apellidos, g.curso, g.paralelo 
    FROM asignaciones a
    INNER JOIN estudiantes est ON a.grado_id = est.grado_id
    INNER JOIN personas per ON est.persona_id = per.id_persona
    INNER JOIN grados g ON est.grado_id = g.id_grado
    WHERE a.docente_id = '$id_docente'";
$query_est = $pdo->prepare($sql_estudiantes);
$query_est->execute();
$estudiantes = $query_est->fetchAll(PDO::FETCH_ASSOC);

print_r($estudiantes);
