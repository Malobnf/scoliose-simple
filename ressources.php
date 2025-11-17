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

// RÉCUPÉRER UTILISATEUR + RÔLE
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

if ($roleName === 'ROLE_ADMIN')  { header('Location: admin.php'); exit; }
if ($roleName === 'ROLE_TEACHER'){ header('Location: profil_enseignant.php'); exit; }

// ==============================================
// 1) Récupérer la classe de l’élève connecté
// ==============================================
$sqlClass = "
    SELECT c.id, c.name
    FROM classes c
    JOIN class_students cs ON cs.class_id = c.id
    WHERE cs.student_id = :sid
    LIMIT 1
";
$stmt = $pdo->prepare($sqlClass);
$stmt->execute([':sid' => $userId]);
$class = $stmt->fetch();

$classId   = $class['id']   ?? null;
$className = $class['name'] ?? 'Classe non définie';


// ==============================================
// 2) Récupérer les ressources de la classe
// ==============================================
$ressources = [];

if ($classId) {
    $sqlRessources = "
        SELECT id, title, type, file_path, created_at
        FROM resources
        WHERE class_id = :cid
        ORDER BY created_at DESC
    ";
    $stmt = $pdo->prepare($sqlRessources);
    $stmt->execute([':cid' => $classId]);
    $ressources = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Ressources</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/notifications.js" defer></script>
</head>
<body>
<header class="site-header">
  <button class="burger-btn" id="burger-btn">
    <i class="fa-solid fa-bars"></i>
  </button>

  <h1 class="site-title">ScoliOse</h1>

  <nav class="mobile-menu" id="mobile-menu">
    <a href="accueil.php" class="mobile-menu_link">Accueil</a>
    <a href="messagerie.php" class="mobile-menu_link">Messagerie</a>
    <a href="profil.php" class="mobile-menu_link">Profil</a>
    <a href="logout.php" class="mobile-menu_link">Déconnexion</a>
  </nav>

  <button class="nav-toggle" id="notif-btn">
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
        <h2>Ressources – <?= htmlspecialchars($className) ?></h2>
        <p style="font-size:0.9rem; color:#6b7280;">
          Voici les documents, liens et vidéos mis à disposition par tes enseignants.
        </p>

        <?php if (empty($ressources)): ?>
            <p>Aucune ressource disponible pour le moment.</p>
        <?php else: ?>
            <ul class="notifications-list">
              <?php foreach ($ressources as $r): ?>
                <li class="notification-card">
                  <h3 class="notification-card__title">
                    <?= htmlspecialchars($r['title']) ?>
                  </h3>

                  <p class="notification-card__meta">
                    Type : <?= htmlspecialchars($r['type']) ?>
                    <br>
                    Ajouté le : <?= htmlspecialchars($r['created_at']) ?>
                  </p>

                  <?php if ($r['file_path']): ?>
                    <a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank"
                       class="notification-card__link">Télécharger</a>
                  <?php else: ?>
                    <span style="color:#888;font-size:0.8rem;">Aucun fichier associé</span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
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
