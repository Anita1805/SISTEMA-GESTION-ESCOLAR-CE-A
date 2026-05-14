<?php
include('app/config.php');
try {
    $pdo->exec("ALTER TABLE material_apoyo ADD COLUMN materia_id INT NULL AFTER usuario_id;");
    echo "Column materia_id added successfully.";
} catch(PDOException $e) {
    if ($e->getCode() == '42S21') { // Column already exists
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
