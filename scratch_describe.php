<?php
include('app/config.php');
echo "estado_cuenta structure:\n";
$stmt = $pdo->query('DESCRIBE estado_cuenta');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "pagos structure:\n";
$stmt = $pdo->query('DESCRIBE pagos');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
