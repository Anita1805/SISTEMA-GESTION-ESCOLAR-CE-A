<?php
include('app/config.php');

try {
    $pdo->beginTransaction();

    $fechaHora = date('Y-m-d H:i:s');
    $estado = '1';

    // Helper functions
    function insertRol($pdo, $nombre, $fyh, $estado) {
        $stmt = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = ?");
        $stmt->execute([$nombre]);
        if($row = $stmt->fetch()) return $row['id_rol'];
        $pdo->prepare("INSERT INTO roles (nombre_rol, fyh_creacion, estado) VALUES (?, ?, ?)")->execute([$nombre, $fyh, $estado]);
        return $pdo->lastInsertId();
    }
    
    function insertUser($pdo, $rol_id, $email, $password, $fyh, $estado) {
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if($row = $stmt->fetch()) return $row['id_usuario'];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) VALUES (?, ?, ?, ?, ?)")->execute([$rol_id, $email, $hash, $fyh, $estado]);
        return $pdo->lastInsertId();
    }
    
    function insertPersona($pdo, $user_id, $nombres, $apellidos, $ci, $fyh, $estado) {
        $stmt = $pdo->prepare("SELECT id_persona FROM personas WHERE ci = ?");
        $stmt->execute([$ci]);
        if($row = $stmt->fetch()) return $row['id_persona'];
        $pdo->prepare("INSERT INTO personas (usuario_id, nombres, apellidos, ci, fecha_nacimiento, profesion, direccion, celular, fyh_creacion, estado) VALUES (?, ?, ?, ?, '1980-01-01', 'Ninguna', 'Sin dirección', '00000000', ?, ?)")->execute([$user_id, $nombres, $apellidos, $ci, $fyh, $estado]);
        return $pdo->lastInsertId();
    }

    $rol_docente_id = insertRol($pdo, 'DOCENTE', $fechaHora, $estado);
    $rol_estudiante_id = insertRol($pdo, 'ESTUDIANTE', $fechaHora, $estado);
    $rol_padre_id = insertRol($pdo, 'PADRE DE FAMILIA', $fechaHora, $estado);
    
    // 1. Crear Gestión, Nivel y Grado
    $stmt = $pdo->query("SELECT id_gestion FROM gestiones LIMIT 1");
    $id_gestion = $stmt->fetchColumn();
    if (!$id_gestion) {
        $pdo->prepare("INSERT INTO gestiones (gestion, fyh_creacion, estado) VALUES ('2026', ?, ?)")->execute([$fechaHora, $estado]);
        $id_gestion = $pdo->lastInsertId();
    }

    $stmt = $pdo->query("SELECT id_nivel FROM niveles LIMIT 1");
    if(!($id_nivel = $stmt->fetchColumn())){
        $pdo->prepare("INSERT INTO niveles (gestion_id, nivel, turno, fyh_creacion, estado) VALUES (?, 'SECUNDARIA', 'MAÑANA', ?, ?)")->execute([$id_gestion, $fechaHora, $estado]);
        $id_nivel = $pdo->lastInsertId();
    }

    $stmt = $pdo->query("SELECT id_grado FROM grados LIMIT 1");
    if(!($id_grado = $stmt->fetchColumn())){
        $pdo->prepare("INSERT INTO grados (nivel_id, curso, paralelo, fyh_creacion, estado) VALUES (?, '5TO AÑO', 'A', ?, ?)")->execute([$id_nivel, $fechaHora, $estado]);
        $id_grado = $pdo->lastInsertId();
    }
    
    // 2. Crear Materias Extras
    $materias = ['Matemáticas Avanzadas', 'Física Moderna', 'Historia Universal', 'Química Orgánica', 'Biología Celular', 'Literatura Contemporánea'];
    $materia_ids = [];
    foreach($materias as $m) {
        $stmt = $pdo->prepare("SELECT id_materia FROM materias WHERE nombre_materia = ?");
        $stmt->execute([$m]);
        if($row = $stmt->fetch()) {
            $materia_ids[] = $row['id_materia'];
        } else {
            $pdo->prepare("INSERT INTO materias (nombre_materia, fyh_creacion, estado) VALUES (?, ?, ?)")->execute([$m, $fechaHora, $estado]);
            $materia_ids[] = $pdo->lastInsertId();
        }
    }

    // 3. Crear Docentes Extras
    $docentes_data = [
        ['email' => 'profe.mates@escuela.com', 'nombres' => 'Albert', 'apellidos' => 'Einstein', 'ci' => '1111111'],
        ['email' => 'profe.historia@escuela.com', 'nombres' => 'Herodoto', 'apellidos' => 'Halicarnaso', 'ci' => '2222222'],
        ['email' => 'profe.quimica@escuela.com', 'nombres' => 'Marie', 'apellidos' => 'Curie', 'ci' => '33333331'],
        ['email' => 'profe.literatura@escuela.com', 'nombres' => 'Gabriel', 'apellidos' => 'Garcia', 'ci' => '44444441']
    ];
    $docente_ids = [];
    foreach($docentes_data as $i => $d) {
        $uid = insertUser($pdo, $rol_docente_id, $d['email'], '12345', $fechaHora, $estado);
        $pid = insertPersona($pdo, $uid, $d['nombres'], $d['apellidos'], $d['ci'], $fechaHora, $estado);
        $stmt = $pdo->prepare("SELECT id_docente FROM docentes WHERE persona_id = ?");
        $stmt->execute([$pid]);
        if($row = $stmt->fetch()){
            $docente_ids[] = $row['id_docente'];
        } else {
            $pdo->prepare("INSERT INTO docentes (persona_id, especialidad, antiguedad, fyh_creacion, estado) VALUES (?, 'Educación', '5 años', ?, ?)")->execute([$pid, $fechaHora, $estado]);
            $docente_ids[] = $pdo->lastInsertId();
        }
    }

    // 4. Asignaciones 
    $asig = [
        [$docente_ids[0], $materia_ids[0]],
        [$docente_ids[0], $materia_ids[1]],
        [$docente_ids[1], $materia_ids[2]],
        [$docente_ids[2], $materia_ids[3]],
        [$docente_ids[2], $materia_ids[4]],
        [$docente_ids[3], $materia_ids[5]]
    ];
    foreach($asig as $a) {
        $stmt = $pdo->prepare("SELECT id_asignacion FROM asignaciones WHERE docente_id = ? AND materia_id = ? AND grado_id = ?");
        $stmt->execute([$a[0], $a[1], $id_grado]);
        if(!$stmt->fetch()){
            $pdo->prepare("INSERT INTO asignaciones (docente_id, nivel_id, grado_id, materia_id, fyh_creacion, estado) VALUES (?, ?, ?, ?, ?, ?)")->execute([$a[0], $id_nivel, $id_grado, $a[1], $fechaHora, $estado]);
        }
    }

    // 6. Estudiantes y Padres Extras
    $estudiantes_data = [
        ['email' => 'alumno1@escuela.com', 'nombres' => 'Juan', 'apellidos' => 'Pérez', 'ci' => '3333333'],
        ['email' => 'alumno2@escuela.com', 'nombres' => 'Maria', 'apellidos' => 'Gomez', 'ci' => '4444444'],
        ['email' => 'alumno3@escuela.com', 'nombres' => 'Luis', 'apellidos' => 'Torres', 'ci' => '5555555'],
        ['email' => 'alumno4@escuela.com', 'nombres' => 'Ana', 'apellidos' => 'Martinez', 'ci' => '66666661'],
        ['email' => 'alumno5@escuela.com', 'nombres' => 'Carlos', 'apellidos' => 'Lopez', 'ci' => '77777771'],
        ['email' => 'alumno6@escuela.com', 'nombres' => 'Sofia', 'apellidos' => 'Ramirez', 'ci' => '88888881'],
        ['email' => 'alumno7@escuela.com', 'nombres' => 'Pedro', 'apellidos' => 'Sanchez', 'ci' => '99999991']
    ];
    $padres_data = [
        ['email' => 'padre1@escuela.com', 'nombres_ppff' => 'Carlos Pérez', 'ci' => '6666666'],
        ['email' => 'padre2@escuela.com', 'nombres_ppff' => 'Ana Gomez', 'ci' => '7777777'],
        ['email' => 'padre3@escuela.com', 'nombres_ppff' => 'Jose Torres', 'ci' => '8888888'],
        ['email' => 'padre4@escuela.com', 'nombres_ppff' => 'Julio Martinez', 'ci' => '1112223'],
        ['email' => 'padre5@escuela.com', 'nombres_ppff' => 'Rosa Lopez', 'ci' => '2223334'],
        ['email' => 'padre6@escuela.com', 'nombres_ppff' => 'Mario Ramirez', 'ci' => '3334445'],
        ['email' => 'padre7@escuela.com', 'nombres_ppff' => 'Elena Sanchez', 'ci' => '4445556']
    ];

    $estudiante_ids = [];

    foreach($estudiantes_data as $i => $e) {
        // Estudiante
        $uid = insertUser($pdo, $rol_estudiante_id, $e['email'], '12345', $fechaHora, $estado);
        $pid = insertPersona($pdo, $uid, $e['nombres'], $e['apellidos'], $e['ci'], $fechaHora, $estado);
        $stmt = $pdo->prepare("SELECT id_estudiante FROM estudiantes WHERE persona_id = ?");
        $stmt->execute([$pid]);
        if($row = $stmt->fetch()){
            $eid = $row['id_estudiante'];
        } else {
            $pdo->prepare("INSERT INTO estudiantes (persona_id, nivel_id, grado_id, rude, fyh_creacion, estado) VALUES (?, ?, ?, '12345678', ?, ?)")->execute([$pid, $id_nivel, $id_grado, $fechaHora, $estado]);
            $eid = $pdo->lastInsertId();
        }
        $estudiante_ids[] = $eid;
        
        // Padre
        $p = $padres_data[$i];
        $puid = insertUser($pdo, $rol_padre_id, $p['email'], '12345', $fechaHora, $estado);
        $stmt = $pdo->prepare("SELECT id_ppff FROM ppffs WHERE estudiante_id = ?");
        $stmt->execute([$eid]);
        if(!$stmt->fetch()){
            $pdo->prepare("INSERT INTO ppffs (usuario_id, estudiante_id, nombres_apellidos_ppff, ci_ppf, celular_ppff, ocupacion_ppff, ref_nombre, ref_parentezco, ref_celular, fyh_creacion, estado) VALUES (?, ?, ?, ?, '000000', 'Ninguna', 'Referencia', 'Tio', '000000', ?, ?)")->execute([$puid, $eid, $p['nombres_ppff'], $p['ci'], $fechaHora, $estado]);
        }
    }

    // 7. Insertar Reportes en Kardex (para graficos)
    $observaciones = ['DISCIPLINA', 'ASISTENCIA', 'RENDIMIENTO ACADÉMICO'];
    
    // Agregamos varios reportes aleatorios
    for($i=0; $i<15; $i++){
        $doc = $docente_ids[array_rand($docente_ids)];
        $est = $estudiante_ids[array_rand($estudiante_ids)];
        // Obtenemos materia del docente
        $mat = null;
        foreach($asig as $a) {
            if($a[0] == $doc) { $mat = $a[1]; break; }
        }
        
        $obs = $observaciones[array_rand($observaciones)];
        $nota_texto = "Reporte autogenerado para pruebas del Kardex ($i)";
        $fecha_random = date('Y-m-d', strtotime('-'.rand(1,30).' days'));
        
        $stmt = $pdo->prepare("INSERT INTO kardexs (docente_id, estudiante_id, materia_id, fecha, observacion, nota, fyh_creacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$doc, $est, $mat, $fecha_random, $obs, $nota_texto, $fechaHora, $estado]);
    }

    $pdo->commit();
    echo "¡Seeding ampliado completado con éxito!";
} catch(Exception $e) {
    $pdo->rollBack();
    echo "Error en el seeding: " . $e->getMessage();
}
?>
