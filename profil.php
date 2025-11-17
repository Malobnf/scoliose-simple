<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/inc/db.php';
session_start();

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// Récupérer les infos
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

// Message si pas d'information
function valueOrNone(?string $value): string {
    $v = trim((string)$value);
    return $v === '' ? 'Aucune information disponible' : $v;
}

// Récupérer photo de profil si en bdd sinon garder l'image de base
$avatarUrl = $student['avatar_url'] ?? 'Images/avatar-1577909.svg';


// Afficher les informations stockées en bdd
$firstNameRaw = $student['first_name'] ?? '';
$lastNameRaw  = $student['last_name'] ?? '';

$fullNameRaw  = trim($firstNameRaw . ' ' . $lastNameRaw);
$fullName     = $fullNameRaw !== '' ? $fullNameRaw : 'Aucune information disponible';

$className    = valueOrNone($student['class_name'] ?? '');
$email        = valueOrNone($student['email'] ?? '');

// Si infos pas encore renseignées 
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
  <title>ScoliOse – Profil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <!-- <script src="Js/api-mock.js" defer></script> -->
  <script src="Js/profil.js" defer></script>
  <script src="Js/notifications.js" defer></script>
  <script src="Js/messagerie.js" defer></script>
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
    <a href="logout.php" class="mobile-menu_link">Déconnexion</a>
  </nav>

  <!-- 🔴 IMPORTANT : id="notif-btn" -->
  <button class="nav-toggle" id="notif-btn" aria-label="Ouvrir les notifications" aria-expanded="false" aria-controls="notif-menu">
    <i class="fa-regular fa-bell"></i>
  </button>

  <!-- 🔴 IMPORTANT : menu + liste avec bons ids -->
  <div class="notif-menu" id="notif-menu" aria-hidden="true">
    <div class="notif-menu__header">
      <span>Notifications</span>
      <a href="notifications.php" class="notif-menu__all">Tout voir</a>
    </div>
    <ul class="notif-menu__list" id="notif-menu-list"></ul>
  </div>
</header>


<main class="profile-main" id="profile-page">
  <section class="hero hero--profile">
    <div class="hero-text">
      <h1 id="profile-name-hero"><?= htmlspecialchars($fullName) ?></h1>
      <h2 id="profile-role-class">Élève – <?= htmlspecialchars($className) ?></h2>
    </div>
    <div class="hero-photo">
      <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Illustration icône de profil" class="image">
    </div>
  </section>

  <section class="profile-layout">
    <section class="profile-column profile-column--left">
      <article class="profile-card">
        <h2>Informations de l’élève</h2>
        <dl class="profile-fields">
          <div class="profile-field">
            <dt>Nom</dt>
            <dd id="profile-name"><?= htmlspecialchars($fullName) ?></dd>
          </div>
          <div class="profile-field">
            <dt>Classe</dt>
            <dd id="profile-class"><?= htmlspecialchars($className) ?></dd>
          </div>
          <div class="profile-field">
            <dt>Établissement</dt>
            <dd id="profile-school">Collège</dd>
          </div>
          <div class="profile-field">
            <dt>Email</dt>
            <dd id="profile-email"><?= htmlspecialchars($email) ?></dd>
          </div>
        </dl>
      </article>

      <article class="profile-card">
        <h2>Responsable légal</h2>
        <dl class="profile-fields">
          <div class="profile-field">
            <dt>Nom</dt>
            <dd id="guardian-name">Nom du responsable</dd>
          </div>
          <div class="profile-field">
            <dt>Téléphone</dt>
            <dd id="guardian-phone">06 00 00 00 00</dd>
          </div>
          <div class="profile-field">
            <dt>Email</dt>
            <dd id="guardian-email">parent@example.com</dd>
          </div>
        </dl>
      </article>
    </section>

    <section class="profile-column profile-column--right">
      <article class="profile-card">
        <h2>Scolarité</h2>
        <dl class="profile-fields">
          <div class="profile-field">
            <dt>Niveau</dt>
            <dd id="profile-level">Collège</dd>
          </div>
          <div class="profile-field">
            <dt>Année scolaire</dt>
            <dd id="profile-year">2024–2025</dd>
          </div>
          <div class="profile-field">
            <dt>Professeur principal</dt>
            <dd id="profile-main-teacher">Prof. Nom</dd>
          </div>
        </dl>
      </article>

      <article class="profile-card">
        <h2>Dernières notes</h2>
        <button class="btn btn-primary profile-grades-button">
          Voir le détail des notes
        </button>
      </article>
    </section>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
