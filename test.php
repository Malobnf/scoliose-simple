<?php
require __DIR__ . '/inc/db.php';

$stmt = $pdo->query('SELECT name, label FROM roles');
$roles = $stmt->fetchAll();

echo '<pre>';
var_dump($roles);
echo '</pre>';
