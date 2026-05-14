<?php
include('app/config.php');
$stmt = $pdo->query('SHOW CREATE TABLE horarios');
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
