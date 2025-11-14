<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Profil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/api-mock.js" defer></script>
  <script src="Js/profil.js" defer></script>
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

<main class="profile-main" id="profile-page">
  <section class="hero hero--profile">
    <div class="hero-text">
      <h1 id="profile-name-hero">Nom de l'élève</h1>
      <h2 id="profile-role-class">Élève – Classe</h2>
      <p class="hero-subtitle" id="profile-tagline">
        Suivi personnalisé de votre scolarité : notes, devoirs, messagerie et ressources.
      </p>
    </div>
    <div class="hero-photo">
      <img src="Images/avatar-1577909.svg" alt="Illustration icône de profil" class="image">
    </div>
  </section>

  <!-- Layout profil -->
  <section class="profile-layout">
    <!-- Colonne gauche : infos élèves / responsables -->
    <section class="profile-column profile-column--left">
      <article class="profile-card">
        <h2>Informations de l’élève</h2>
        <dl class="profile-fields">
          <div class="profile-field">
            <dt>Nom</dt>
            <dd id="profile-name">Nom de l'élève</dd>
          </div>
          <div class="profile-field">
            <dt>Classe</dt>
            <dd id="profile-class">4ème B</dd>
          </div>
          <div class="profile-field">
            <dt>Établissement</dt>
            <dd id="profile-school">Collège</dd>
          </div>
          <div class="profile-field">
            <dt>Email</dt>
            <dd id="profile-email">prenom.nom@example.com</dd>
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

    <!-- Colonne droite : scolarité / notes -->
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
        <ul class="profile-grades-list" id="profile-grades-list">
          <li class="profile-grade-item">
            <span class="profile-grade-subject">Maths</span>
            <span class="profile-grade-value">16/20</span>
            <span class="profile-grade-date">12/11</span>
          </li>
        </ul>
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
