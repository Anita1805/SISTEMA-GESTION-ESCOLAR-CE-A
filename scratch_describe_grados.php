<?php
include('app/config.php');
$stmt = $pdo->query('DESCRIBE grados');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
