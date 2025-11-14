<?php
require __DIR__ . '/inc/db.php';

$userId = 1;

// Récupérer les infos de l'élève
$sql = "
  SELECT u.email,
    sp.first_name,
    sp.last_name,
    sp.class_name,
  FROM users u
  JOIN student_profiles sp ON sp.user_id = u.id
  WHERE u.id = :uid
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $userId]);
$student = $stmt->fetch();

// Valeurs par défaut si vide
$firstName  = $student['first_name'] ?? 'Élève';
$lastName   = $student['last_name'] ?? '';
$fullName   = trim($firstName . ' ' . $lastName);
$className  = $student['class_name'] ?? 'Classe non définie';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
    <title>ScoliOse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="Js/app.js" defer></script>
    <script src="Js/api-mock.js" defer></script>
    <script src="Js/notifications.js" defer></script>
</head>

<body>
<header class="site-header">
  <button class="burger-btn" id="burger-btn" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-menu">
    <i class="fa-solid fa-bars"></i>
  </button>

  <h1 class="site-title">ScoliOse</h1>

  <nav class="mobile-menu" id="mobile-menu">
    <a href="accueil.php" class="mobile-menu_link">Accueil</a>
    <a href="messagerie.php" class="mobile-menu_link">Messagerie</a>
    <a href="profil.php" class="mobile-menu_link">Profil</a>
    <a href="index.php" class="mobile-menu_link">Déconnexion</a>
  </nav>

  <button class="nav-toggle" id="notif-btn" aria-label="Ouvrir les notifications" aria-expanded="false" aria-controls="notif-menu"><i class="fa-regular fa-bell"></i></button>
  <div class="notif-menu" id="notif-menu" aria-hidden="true">
  <div class="notif-menu__header">
    <span>Notifications</span>
    <a href="notifications.html" class="notif-menu__all">Tout voir</a>
  </div>
  <ul class="notif-menu__list" id="notif-menu-list"></ul>
</div>
</header>

<main>
  <section class="hero">
    <div class="hero-text">
      <h1>Bienvenue</h1>
      <h2><?= htmlspecialchars($fullName) ?></h2>
      <p>Classe : <?= htmlspecialchars($className) ?></p>
      <a href="profil.html" class="btn btn-primary">Consulter le profil</a>
    </div>

    <div class="hero-photo">
      <img src="Images/avatar-1577909.svg" alt="Illustration icône de profil" class="image">
    </div>
  </section>

  <section class="features">
    <article class="feature-card">
            <h2>Devoirs à faire</h2>
            <img src="Images/pen-6238177.svg" alt="Illustration papier et stylo" class="image">
        </article>
        <article class="feature-card">
            <h2>Emploi du temps</h2>
            <img src="Images/calender-2389150_1280.png" alt="Illustration agenda" class="image">
        </article>
        <article class="feature-card">
            <h2>Suivi de notes</h2>
            <img src="Images/result-6015355.svg" alt="Illustration examen" class="image">
        </article>
        <article class="feature-card">
            <h2>Ressources</h2>
            <img src="Images/book-4759551.svg" alt="Illustration livres" class="image">
        </article>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - MonSite</p>
</footer>

</body>
</html>