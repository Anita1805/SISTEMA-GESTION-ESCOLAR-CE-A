<?php
include('app/config.php');

try {
    $pdo->beginTransaction();

    // Encontrar personas de prueba (fyh_creacion < '2025-01-01' AND id_persona > 1)
    $stmt = $pdo->prepare("SELECT id_persona, usuario_id FROM personas WHERE fyh_creacion < '2025-01-01' AND id_persona > 1");
    $stmt->execute();
    $fake_personas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach($fake_personas as $p) {
        $id_persona = $p['id_persona'];
        $id_usuario = $p['usuario_id'];

        // Buscar estudiante
        $stmt_est = $pdo->prepare("SELECT id_estudiante FROM estudiantes WHERE persona_id = :id_persona");
        $stmt_est->execute(['id_persona' => $id_persona]);
        $est = $stmt_est->fetch(PDO::FETCH_ASSOC);

        if($est) {
            $id_estudiante = $est['id_estudiante'];
            $pdo->prepare("DELETE FROM ppffs WHERE estudiante_id = :id")->execute(['id' => $id_estudiante]);
            $pdo->prepare("DELETE FROM calificaciones WHERE estudiante_id = :id")->execute(['id' => $id_estudiante]);
            $pdo->prepare("DELETE FROM estudiantes WHERE id_estudiante = :id")->execute(['id' => $id_estudiante]);
        }

        // Buscar docente
        $stmt_doc = $pdo->prepare("SELECT id_docente FROM docentes WHERE persona_id = :id_persona");
        $stmt_doc->execute(['id_persona' => $id_persona]);
        $doc = $stmt_doc->fetch(PDO::FETCH_ASSOC);
        
        if($doc) {
            $id_docente = $doc['id_docente'];
            $pdo->prepare("DELETE FROM asignaciones WHERE docente_id = :id")->execute(['id' => $id_docente]);
            $pdo->prepare("DELETE FROM docentes WHERE id_docente = :id")->execute(['id' => $id_docente]);
        }
        
        // Buscar ppffs directly linked to usuario
        if($id_usuario) {
            $pdo->prepare("DELETE FROM ppffs WHERE usuario_id = :id")->execute(['id' => $id_usuario]);
        }

        // Borrar persona
        $pdo->prepare("DELETE FROM personas WHERE id_persona = :id")->execute(['id' => $id_persona]);
        
        // Borrar usuario si existe
        if($id_usuario) {
            $pdo->prepare("DELETE FROM mensajes_chat WHERE remitente_id = :id OR destinatario_id = :id")->execute(['id' => $id_usuario]);
            $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id")->execute(['id' => $id_usuario]);
        }
        
        $count++;
    }

    $pdo->commit();
    echo "Borrados $count usuarios de prueba correctamente.";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
