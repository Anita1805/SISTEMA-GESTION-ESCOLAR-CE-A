<?php
include('app/config.php');
try {
    $pdo->exec("RENAME TABLE pagos TO estado_cuenta");
    echo "Table renamed to estado_cuenta successfully.";
} catch (PDOException $e) {
    echo "Error renaming table: " . $e->getMessage();
}
?>
