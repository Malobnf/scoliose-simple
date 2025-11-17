<?php
require __DIR__ . '/inc/db.php';

$userId = 1;

$sql = "
    SELECT id, title, body, url, created_at, is_read
    FROM notifications
    WHERE user_id = :uid
    ORDER BY datetime(created_at) DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $userId]);
$notifications = $stmt->fetchAll();

$highlightId = isset($_GET['id']) ? (int) $_GET['id'] : null;
?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Notifications</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/api-mock.js" defer></script>
  <script src="Js/notifications.js" defer></script>
  <script src="Js/profil.js" defer></script>
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

<main class="notifications-main">
  <section class="hero hero--messaging">
    <div class="hero-text">
      <h1>Notifications</h1>
      <h2>Historique de vos dernières notifications</h2>
      <p class="hero-subtitle">
        Retrouvez ici toutes les notifications reçues, classées par ordre chronologique.
      </p>
    </div>
  </section>

  <section class="notifications-section">
    <div class="notifications-list">
      <?php if (empty($notifications)): ?>
        <p>Aucune notification pour le moment.</p>
      <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
          <?php
            $isHighlight = ($highlightId && $highlightId === (int)$notif['id']);
          ?>
          <article
            class="notification-card<?= $isHighlight ? ' notification-card--highlight' : '' ?>"
            id="notif-<?= (int)$notif['id'] ?>"
          >
            <h3 class="notification-card__title">
              <?= htmlspecialchars($notif['title']) ?>
            </h3>

            <p class="notification-card__meta">
              <?= htmlspecialchars(date('d/m/Y H:i', strtotime($notif['created_at']))) ?>
            </p>

            <?php if (!empty($notif['body'])): ?>
              <p class="notification-card__body">
                <?= nl2br(htmlspecialchars($notif['body'])) ?>
              </p>
            <?php endif; ?>

            <?php if (!empty($notif['url'])): ?>
              <a href="<?= htmlspecialchars($notif['url']) ?>" class="notification-card__link">
                Aller au contenu
              </a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>