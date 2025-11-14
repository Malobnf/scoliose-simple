<?php
// messagerie.php
require __DIR__ . '/inc/db.php';

// TODO plus tard : userId depuis la session
$userId = 1;

// Récupérer les conversations où l'utilisateur participe
$sqlConvs = "
    SELECT c.id, c.title, c.type, c.created_at
    FROM conversations c
    JOIN conversation_participants cp ON cp.conversation_id = c.id
    WHERE cp.user_id = :uid
    ORDER BY datetime(c.created_at) DESC
";
$stmt = $pdo->prepare($sqlConvs);
$stmt->execute([':uid' => $userId]);
$conversations = $stmt->fetchAll();

// Déterminer la conversation courante
$currentConvId = null;
if (isset($_GET['conversation_id'])) {
    $currentConvId = (int) $_GET['conversation_id'];
} elseif (!empty($conversations)) {
    $currentConvId = (int) $conversations[0]['id'];
}

// Récupérer les messages de la conversation courante
$messages = [];
$currentConvTitle = 'Aucune conversation';
if ($currentConvId) {
    $sqlMessages = "
        SELECT m.id, m.body, m.created_at, m.sender_id, u.email
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE m.conversation_id = :cid
        ORDER BY datetime(m.created_at) ASC
    ";
    $stmt = $pdo->prepare($sqlMessages);
    $stmt->execute([':cid' => $currentConvId]);
    $messages = $stmt->fetchAll();

    // Titre de la conversation courante
    foreach ($conversations as $conv) {
        if ((int)$conv['id'] === $currentConvId) {
            $currentConvTitle = $conv['title'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Messagerie</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/messagerie.js" defer></script>
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

  <button class="nav-toggle" aria-label="Notifications">
    <i class="fa-regular fa-bell"></i>
  </button>
</header>

<main class="messaging-main">
  <!-- En-tête de la page -->
  <section class="hero hero--messaging">
    <div class="hero-text">
      <h1>Messagerie</h1>
      <h2>Échanger avec l’équipe pédagogique et vos camarades</h2>
      <p class="hero-subtitle">
        Retrouvez ici toutes vos conversations : devoirs, questions, organisation…
      </p>
    </div>
    <div class="hero-photo">
      <img src="Images/avatar-1577909.svg" alt="Illustration icône de profil" class="image">
    </div>
  </section>

  <!-- Zone de messagerie -->
  <section class="messaging-layout">
    <!-- Liste des conversations -->
    <aside class="conversation-list">
      <div class="conversation-list__header">
        <h2>Conversations</h2>
        <button class="btn-icon" aria-label="Nouvelle conversation">
          <i class="fa-solid fa-plus"></i>
        </button>
      </div>

      <ul class="conversation-list__items">
        <li class="conversation-item conversation-item--active">
          <div class="conversation-item__title">Maths – 4ème B</div>
          <div class="conversation-item__meta">Dernier message : 14:20</div>
        </li>
        <li class="conversation-item">
          <div class="conversation-item__title">Français – Devoir d’écriture</div>
          <div class="conversation-item__meta">Hier · Prof. Dupont</div>
        </li>
        <li class="conversation-item">
          <div class="conversation-item__title">Groupe projet Histoire</div>
          <div class="conversation-item__meta">Lundi · 3 nouveaux messages</div>
        </li>
      </ul>
    </aside>

    <!-- Zone de discussion -->
    <section class="conversation-panel">
      <header class="conversation-header">
        <div>
          <h2>Maths – 4ème B</h2>
          <p class="conversation-header__subtitle">Prof. Martin · Chapitre 3 – Fractions</p>
        </div>
        <button class="btn-icon" aria-label="Options de la conversation">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
      </header>

      <div class="messages">
        <div class="message message--received">
          <div class="message__meta">Prof. Martin · 14:05</div>
          <div class="message__bubble">
            N’oubliez pas de rendre l’exercice 4 page 52 pour demain.
          </div>
        </div>

        <div class="message message--sent">
          <div class="message__meta">Vous · 14:12</div>
          <div class="message__bubble">
            Bonjour, est-ce que l’exercice 3 est aussi obligatoire ?
          </div>
        </div>

        <div class="message message--received">
          <div class="message__meta">Prof. Martin · 14:18</div>
          <div class="message__bubble">
            Non, l’exercice 3 est facultatif, uniquement si vous voulez vous entraîner.
          </div>
        </div>
      </div>

      <form class="message-form">
        <label for="message-input" class="sr-only">Votre message</label>
        <textarea id="message-input" class="message-form__input" rows="2" placeholder="Écrire un message..."></textarea>
        <button type="submit" class="btn btn-primary message-form__submit">
          Envoyer <i class="fa-solid fa-paper-plane"></i>
        </button>
      </form>
    </section>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
