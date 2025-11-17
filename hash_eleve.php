<?php
$plain = 'Prof123!'; // mot de passe commun pour les comptes de démo
echo password_hash($plain, PASSWORD_DEFAULT);
