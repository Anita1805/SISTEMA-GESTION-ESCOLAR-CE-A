<?php
include('app/config.php');

$nombres_list = ["Andres", "Carlos", "Luis", "Maria", "Ana", "Laura", "Pedro", "Juan", "Diego", "Sofia", "Camila", "Jorge", "Mateo", "Valentina", "Isabella", "Daniel", "David", "Mariana", "Victoria", "Gabriel"];
$apellidos_list = ["Garcia", "Martinez", "Rodriguez", "Lopez", "Perez", "Gonzalez", "Gomez", "Fernandez", "Moreno", "Jimenez", "Ruiz", "Diaz", "Alvarez", "Romero", "Alonso", "Gutierrez", "Navarro", "Torres", "Dominguez", "Vargas"];

function getRandomName($list) {
    return $list[array_rand($list)];
}

function getRandomDate() {
    $timestamp = mt_rand(strtotime("2005-01-01"), strtotime("2015-12-31"));
    return date("Y-m-d", $timestamp);
}

try {
    // Determine how many students per grade
    $stmt = $pdo->query("SELECT id_grado, nivel_id FROM grados WHERE estado='1'");
    $grados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT grado_id, COUNT(*) as cant FROM estudiantes WHERE estado='1' GROUP BY grado_id");
    $est_counts_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $est_counts = [];
    foreach($est_counts_raw as $c) {
        $est_counts[$c['grado_id']] = $c['cant'];
    }

    $pdo->beginTransaction();

    $added_students = 0;

    foreach ($grados as $grado) {
        $grado_id = $grado['id_grado'];
        $nivel_id = $grado['nivel_id'];
        $current = isset($est_counts[$grado_id]) ? $est_counts[$grado_id] : 0;
        $needed = 15 - $current;

        for ($i = 0; $i < $needed; $i++) {
            $nombres = getRandomName($nombres_list) . " " . getRandomName($nombres_list);
            $apellidos = getRandomName($apellidos_list) . " " . getRandomName($apellidos_list);
            $ci = mt_rand(10000000, 99999999);
            $email = strtolower(str_replace(' ', '', $nombres)) . $ci . "@escuela.com";
            $fecha_nacimiento = getRandomDate();
            $celular = mt_rand(3000000000, 3999999999);
            $direccion = "Calle " . mt_rand(1, 100) . " # " . mt_rand(1, 100) . "-" . mt_rand(1, 100);
            $rude = "RUDE" . mt_rand(100000, 999999);
            $estado_de_registro = '1';

            // ESTUDIANTE USER
            $rol_id = 7;
            $password = password_hash((string)$ci, PASSWORD_DEFAULT);
            $sentencia = $pdo->prepare('INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) VALUES (:rol_id,:email,:password,:fyh_creacion,:estado)');
            $sentencia->execute(['rol_id'=>$rol_id, 'email'=>$email, 'password'=>$password, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
            $id_usuario = $pdo->lastInsertId();

            // ESTUDIANTE PERSONA
            $sentencia = $pdo->prepare('INSERT INTO personas (usuario_id,nombres,apellidos,ci,fecha_nacimiento,celular,profesion,direccion, fyh_creacion, estado) VALUES (:usuario_id,:nombres,:apellidos,:ci,:fecha_nacimiento,:celular,:profesion,:direccion,:fyh_creacion,:estado)');
            $sentencia->execute(['usuario_id'=>$id_usuario, 'nombres'=>$nombres, 'apellidos'=>$apellidos, 'ci'=>$ci, 'fecha_nacimiento'=>$fecha_nacimiento, 'celular'=>$celular, 'profesion'=>"ESTUDIANTE", 'direccion'=>$direccion, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
            $id_persona = $pdo->lastInsertId();

            // ESTUDIANTE
            $sentencia = $pdo->prepare('INSERT INTO estudiantes (persona_id, nivel_id, grado_id, rude, fyh_creacion, estado) VALUES (:persona_id,:nivel_id,:grado_id,:rude,:fyh_creacion,:estado)');
            $sentencia->execute(['persona_id'=>$id_persona, 'nivel_id'=>$nivel_id, 'grado_id'=>$grado_id, 'rude'=>$rude, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
            $id_estudiante = $pdo->lastInsertId();

            // PADRE DE FAMILIA
            $nombres_p = getRandomName($nombres_list);
            $apellidos_p = $apellidos; // Same last name
            $ci_p = mt_rand(10000000, 99999999);
            $email_p = strtolower($nombres_p . $apellidos_p) . $ci_p . "@padres.com";
            $celular_p = mt_rand(3000000000, 3999999999);
            $ocupacion_p = "EMPLEADO";

            // PPFF USER
            $rol_ppff_id = 8;
            $password_ppff = password_hash((string)$ci_p, PASSWORD_DEFAULT);
            $sentencia_usu_p = $pdo->prepare('INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) VALUES (:rol_id,:email,:password,:fyh_creacion,:estado)');
            $sentencia_usu_p->execute(['rol_id'=>$rol_ppff_id, 'email'=>$email_p, 'password'=>$password_ppff, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
            $id_usuario_ppff = $pdo->lastInsertId();

            // PPFF PERSONA
            $sentencia_per_p = $pdo->prepare('INSERT INTO personas (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, profesion, direccion, fyh_creacion, estado) VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, :profesion, :direccion, :fyh_creacion, :estado)');
            $sentencia_per_p->execute(['usuario_id'=>$id_usuario_ppff, 'nombres'=>$nombres_p, 'apellidos'=>$apellidos_p, 'ci'=>$ci_p, 'fecha_nacimiento'=>'1900-01-01', 'celular'=>$celular_p, 'profesion'=>$ocupacion_p, 'direccion'=>'', 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);

            // PPFF
            $nombres_apellidos_ppff = $nombres_p . " " . $apellidos_p;
            $sentencia = $pdo->prepare('INSERT INTO ppffs (estudiante_id , usuario_id, nombres_apellidos_ppff, ci_ppf, celular_ppff, ocupacion_ppff, ref_nombre, ref_parentezco, ref_celular, fyh_creacion, estado) VALUES (:estudiante_id , :usuario_id, :nombres_apellidos_ppff,:ci_ppf,:celular_ppff,:ocupacion_ppff,:ref_nombre,:ref_parentezco,:ref_celular,:fyh_creacion,:estado)');
            $sentencia->execute(['estudiante_id'=>$id_estudiante, 'usuario_id'=>$id_usuario_ppff, 'nombres_apellidos_ppff'=>$nombres_apellidos_ppff, 'ci_ppf'=>$ci_p, 'celular_ppff'=>$celular_p, 'ocupacion_ppff'=>$ocupacion_p, 'ref_nombre'=>$nombres_p, 'ref_parentezco'=>'PADRE', 'ref_celular'=>$celular_p, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);

            $added_students++;
        }
    }

    echo "Students and parents added: $added_students\n";

    // Now Assignments and Schedules
    $stmt = $pdo->query("SELECT doc.id_docente FROM docentes doc WHERE doc.estado='1'");
    $docentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("SELECT id_materia FROM materias WHERE estado='1'");
    $materias = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $added_asignaciones = 0;
    $added_horarios = 0;

    foreach ($grados as $grado) {
        $grado_id = $grado['id_grado'];
        $nivel_id = $grado['nivel_id'];

        // check if grade has asignaciones
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM asignaciones WHERE grado_id = ? AND estado='1'");
        $stmt->execute([$grado_id]);
        $has_asignaciones = $stmt->fetchColumn();

        if ($has_asignaciones < 3) { // If less than 3, add more
            // Pick random 3 materias
            $random_materias = (array)array_rand(array_flip($materias), 3);
            
            $dias = ["LUNES", "MARTES", "MIERCOLES"];
            $hora_inicio = "08:00:00";
            $hora_fin = "10:00:00";
            
            $dia_index = 0;

            foreach ($random_materias as $mat_id) {
                $docente_id = getRandomName($docentes);

                $sentencia = $pdo->prepare('INSERT INTO asignaciones (docente_id, nivel_id, grado_id, materia_id, fyh_creacion, estado) VALUES (:docente_id, :nivel_id, :grado_id, :materia_id, :fyh_creacion, :estado)');
                $sentencia->execute(['docente_id'=>$docente_id, 'nivel_id'=>$nivel_id, 'grado_id'=>$grado_id, 'materia_id'=>$mat_id, 'fyh_creacion'=>$fechaHora, 'estado'=>$estado_de_registro]);
                
                // create horario
                $sentencia = $pdo->prepare('INSERT INTO horarios (grado_id, materia_id, docente_id, dia_semana, hora_inicio, hora_fin, estado, fyh_creacion) VALUES (:grado_id, :materia_id, :docente_id, :dia_semana, :hora_inicio, :hora_fin, :estado, :fyh_creacion)');
                $sentencia->execute([
                    'grado_id'=>$grado_id,
                    'materia_id'=>$mat_id,
                    'docente_id'=>$docente_id,
                    'dia_semana'=>$dias[$dia_index],
                    'hora_inicio'=>$hora_inicio,
                    'hora_fin'=>$hora_fin,
                    'estado'=>'1',
                    'fyh_creacion'=>$fechaHora
                ]);

                $dia_index++;
                $added_asignaciones++;
                $added_horarios++;
            }
        }
    }

    echo "Asignaciones added: $added_asignaciones\n";
    echo "Horarios added: $added_horarios\n";

    $pdo->commit();
    echo "SUCCESS: All data committed.";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage();
}

?>
