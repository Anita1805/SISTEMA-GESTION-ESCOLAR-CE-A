<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 13/1/2024
 * Time: 07:33
 */

$sql_materias = "SELECT * FROM materias where estado = '1' ";
$query_materias = $pdo->prepare($sql_materias);
$query_materias->execute();
$materias = $query_materias->fetchAll(PDO::FETCH_ASSOC);
