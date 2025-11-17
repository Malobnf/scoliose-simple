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
    SELECT u.email, r.name AS role_name
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

$sqlClass = "
    SELECT c.id, c.name
    FROM classes c
    JOIN class_students cs ON cs.class_id = c.id
    WHERE cs.student_id = :uid
    LIMIT 1
";
$stmt = $pdo->prepare($sqlClass);
$stmt->execute([':uid' => $userId]);
$class = $stmt->fetch();

$classId   = $class['id']   ?? null;
$className = $class['name'] ?? 'Classe non définie';

$joursNoms = [
    1 => 'Lundi',
    2 => 'Mardi',
    3 => 'Mercredi',
    4 => 'Jeudi',
    5 => 'Vendredi',
];

$emplois = [];

if ($classId) {
    $sqlET = "
        SELECT weekday, start_time, end_time, subject, room
        FROM timetable_entries
        WHERE class_id = :cid
        ORDER BY weekday, start_time
    ";
    $stmt = $pdo->prepare($sqlET);
    $stmt->execute([':cid' => $classId]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $w = (int)$row['weekday'];
        if (!isset($emplois[$w])) {
            $emplois[$w] = [];
        }
        $emplois[$w][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Emploi du temps</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
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
    <a href="logout.php" class="mobile-menu_link">Déconnexion</a>
  </nav>

  <button class="nav-toggle" id="notif-btn" aria-label="Ouvrir les notifications" aria-expanded="false" aria-controls="notif-menu">
    <i class="fa-regular fa-bell"></i>
  </button>
  <div class="notif-menu" id="notif-menu" aria-hidden="true">
    <div class="notif-menu__header">
      <span>Notifications</span>
      <a href="notifications.php" class="notif-menu__all">Tout voir</a>
    </div>
    <ul class="notif-menu__list" id="notif-menu-list"></ul>
  </div>
</header>

<main class="profile-main profile-main--center">
  <section class="profile-layout">
    <section class="profile-column">
      <article class="profile-card">
        <h2>Emploi du temps – <?= htmlspecialchars($className) ?></h2>

        <?php if (empty($emplois)): ?>
          <p>Aucun créneau enregistré pour le moment.</p>
        <?php else: ?>
          <div class="public-audience">
            <?php foreach ($joursNoms as $numJour => $nomJour): ?>
              <?php $creneaux = $emplois[$numJour] ?? []; ?>
              <?php if (empty($creneaux)) continue; ?>
              <article class="profile-card">
                <h3 style="margin-top:0;"><?= htmlspecialchars($nomJour) ?></h3>
                <ul style="margin:0; padding-left:18px; font-size:0.9rem;">
                  <?php foreach ($creneaux as $c): ?>
                    <li>
                      <?= htmlspecialchars($c['start_time']) ?>–<?= htmlspecialchars($c['end_time']) ?>
                      : <?= htmlspecialchars($c['subject']) ?>
                      <?php if (!empty($c['room'])): ?>
                        (salle <?= htmlspecialchars($c['room']) ?>)
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>
    </section>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
