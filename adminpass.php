<?php
require __DIR__ . '/inc/db.php';

// Mot de passe que tu veux garder pour l'admin
$newPlainPassword = 'admin'; // tu peux mettre autre chose, ex : 'Admin2025!'

// 1. Générer le hash sécurisé
$hash = password_hash($newPlainPassword, PASSWORD_DEFAULT);

// 2. Mettre à jour la ligne de l’admin dans la table users
$stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE email = :email');
$stmt->execute([
    ':hash'  => $hash,
    ':email' => 'admin@scoliose.fr',
]);

echo "Nouveau hash enregistré pour admin@scoliose.fr :<br>";
echo htmlspecialchars($hash);
