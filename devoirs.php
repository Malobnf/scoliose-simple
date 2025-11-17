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

$devoirs = [];
if ($classId) {
    $sqlDevoirs = "
        SELECT id, subject, title, description, due_date
        FROM homeworks
        WHERE class_id = :cid
        ORDER BY due_date ASC
    ";
    $stmt = $pdo->prepare($sqlDevoirs);
    $stmt->execute([':cid' => $classId]);
    $devoirs = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Devoirs à faire</title>
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
        <h2>Devoirs à faire – <?= htmlspecialchars($className) ?></h2>

        <?php if (empty($devoirs)): ?>
          <p>Aucun devoir enregistré pour le moment.</p>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>Matière</th>
                <th>Titre</th>
                <th>À rendre pour le</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($devoirs as $d): ?>
                <tr>
                  <td><?= htmlspecialchars($d['subject'] ?? 'N/A') ?></td>
                  <td>
                    <?= htmlspecialchars($d['title']) ?><br>
                    <small style="color:#6b7280;">
                      <?= htmlspecialchars($d['description'] ?? '') ?>
                    </small>
                  </td>
                  <td><?= htmlspecialchars($d['due_date'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
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
