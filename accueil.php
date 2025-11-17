<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/inc/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$sqlUser = "
    SELECT 
        u.email,
        r.name AS role_name
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.id = :uid
";
$stmt = $pdo->prepare($sqlUser);
$stmt->execute([':uid' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$roleName = $user['role_name'] ?? '';

if ($roleName === 'ROLE_ADMIN') {
    header('Location: admin.php');
    exit;
}

if ($roleName === 'ROLE_TEACHER') {
    header('Location: profil_enseignant.php');
    exit;
}


$sql = "
    SELECT 
        u.email,
        sp.first_name,
        sp.last_name,
        sp.class_name
    FROM users u
    LEFT JOIN student_profiles sp ON sp.user_id = u.id
    WHERE u.id = :uid
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $userId]);
$student = $stmt->fetch();

function valueOrNone(?string $value): string {
    $v = trim((string)$value);
    return $v === '' ? 'Aucune information disponible' : $v;
}

$avatarUrl = $student['avatar_url'] ?? 'Images/avatar-1577909.svg';

$firstNameRaw = $student['first_name'] ?? '';
$lastNameRaw  = $student['last_name'] ?? '';
$fullNameRaw  = trim($firstNameRaw . ' ' . $lastNameRaw);
$fullName     = $fullNameRaw !== '' ? $fullNameRaw : 'Aucune information disponible';

$className    = valueOrNone($student['class_name'] ?? '');
$email        = valueOrNone($student['email'] ?? '');

$guardianName  = 'Aucune information disponible';
$guardianPhone = 'Aucune information disponible';
$guardianEmail = 'Aucune information disponible';
$schoolName    = 'Aucune information disponible';
$level         = 'Aucune information disponible';
$schoolYear    = 'Aucune information disponible';
$mainTeacher   = 'Aucune information disponible';
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
    <!-- <script src="Js/api-mock.js" defer></script> -->
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
    <a href="notifications.php" class="notif-menu__all">Tout voir</a>
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
      <a href="profil.php" class="btn btn-primary">Consulter le profil</a>
    </div>

    <div class="hero-photo">
      <img src="Images/avatar-1577909.svg" alt="Illustration icône de profil" class="image">
    </div>
  </section>

<section class="features">
  <a href="devoirs.php" class="feature-card" style="text-decoration:none; color:inherit;">
    <h2>Devoirs à faire</h2>
    <img src="Images/pen-6238177.svg" alt="Illustration papier et stylo" class="image">
  </a>

  <a href="emploi_du_temps.php" class="feature-card" style="text-decoration:none; color:inherit;">
    <h2>Emploi du temps</h2>
    <img src="Images/calender-2389150_1280.png" alt="Illustration agenda" class="image">
  </a>

  <a href="notes.php" class="feature-card" style="text-decoration:none; color:inherit;">
    <h2>Suivi de notes</h2>
    <img src="Images/result-6015355.svg" alt="Illustration examen" class="image">
  </a>

  <a href="ressources.php" class="feature-card" style="text-decoration:none; color:inherit;">
    <h2>Ressources</h2>
    <img src="Images/book-4759551.svg" alt="Illustration livres" class="image">
  </a>
</section>

</main>

<footer class="footer">
  <p>&copy; 2025 - MonSite</p>
</footer>

</body>
</html>