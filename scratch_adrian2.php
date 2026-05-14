<?php
include ('app/config.php');

try {
    $query_est = $pdo->query("SELECT est.id_estudiante, per.nombres, per.apellidos, usu.email, pp.nombres as padre_nombres, pp.apellidos as padre_apellidos, up.email as padre_email, pf.id_padre_familia
        FROM estudiantes est
        INNER JOIN personas per ON est.persona_id = per.id_persona
        LEFT JOIN usuarios usu ON per.usuario_id = usu.id_usuario
        LEFT JOIN padres_familias pf ON pf.estudiante_id = est.id_estudiante
        LEFT JOIN personas pp ON pf.persona_id = pp.id_persona
        LEFT JOIN usuarios up ON pp.usuario_id = up.id_usuario
        WHERE per.nombres LIKE '%Adrián%' OR per.apellidos LIKE '%Gil%'");
    $data = $query_est->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data, JSON_PRETTY_PRINT);
} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
