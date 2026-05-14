<?php
include ('app/config.php');

$estudiante_id = 73;
$est_persona_id = 98;
$est_usuario_id = 52;
$ppff_id = 66;
$ppff_usuario_id = 53;

try {
    echo "Starting...<br>";
    $pdo->exec("DELETE FROM observador_alumno WHERE estudiante_id = $estudiante_id");
    echo "observador_alumno deleted.<br>";

    $pdo->exec("DELETE FROM kardexs WHERE estudiante_id = $estudiante_id");
    echo "kardexs deleted.<br>";

    // Not deleting from notas because it does not have estudiante_id.

    $pdo->exec("DELETE FROM ppffs WHERE id_ppff = $ppff_id");
    echo "ppffs deleted.<br>";

    if ($ppff_usuario_id) {
        $pdo->exec("DELETE FROM usuarios WHERE id_usuario = $ppff_usuario_id");
    }
    echo "ppff usuario deleted.<br>";

    $pdo->exec("DELETE FROM estudiantes WHERE id_estudiante = $estudiante_id");
    echo "estudiantes deleted.<br>";

    $pdo->exec("DELETE FROM personas WHERE id_persona = $est_persona_id");
    echo "personas deleted.<br>";

    if ($est_usuario_id) {
        $pdo->exec("DELETE FROM usuarios WHERE id_usuario = $est_usuario_id");
    }
    echo "estudiante usuario deleted.<br>";

    echo "Student 73 deleted successfully.<br>";

} catch (Exception $e) {
    echo "Failed: " . $e->getMessage() . "<br>";
}
