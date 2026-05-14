<?php
include ('app/config.php');

$query = $pdo->query("SELECT d.id_docente FROM usuarios as usu 
    INNER JOIN personas as per ON per.usuario_id = usu.id_usuario 
    INNER JOIN docentes as d ON d.persona_id = per.id_persona");
$docs = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($docs as $doc) {
    $id_docente = $doc['id_docente'];
    $sql_estudiantes = "SELECT DISTINCT est.id_estudiante, per.nombres, per.apellidos, g.curso, g.paralelo 
        FROM asignaciones a
        INNER JOIN estudiantes est ON a.grado_id = est.grado_id
        INNER JOIN personas per ON est.persona_id = per.id_persona
        INNER JOIN grados g ON est.grado_id = g.id_grado
        WHERE a.docente_id = '$id_docente'";
    $query_est = $pdo->prepare($sql_estudiantes);
    $query_est->execute();
    $est = $query_est->fetchAll(PDO::FETCH_ASSOC);
    if (count($est) > 6) { // To see if any teacher has a bunch of duplicates
        echo "Teacher $id_docente has " . count($est) . " students.\n";
    }
}
