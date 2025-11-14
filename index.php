<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Accueil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/api-mock.js" defer></script>
</head>

<body>
<header class="site-header">
  <button class="burger-btn" id="burger-btn" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-menu">
    <i class="fa-solid fa-bars"></i>
  </button>

  <h1 class="site-title">ScoliOse</h1>

  <nav class="mobile-menu" id="mobile-menu">
    <a href="#hero" class="mobile-menu_link">Accueil</a>
    <a href="#features" class="mobile-menu_link">Fonctionnalités</a>
    <a href="#pour-qui" class="mobile-menu_link">Pour qui ?</a>
    <a href="login.html" class="mobile-menu_link">Connexion</a>
  </nav>

  <a href="login.html" class="btn btn-primary header-login-btn">
    Connexion
  </a>
</header>

<main>
  <!-- HERO PUBLIC -->
  <section class="hero hero--public" id="hero">
    <div class="hero-text">
      <h1>ScoliOse</h1>
      <h2>Un espace unique pour suivre la scolarité des élèves</h2>
      <p class="hero-subtitle">
        Notes, devoirs, messagerie, ressources : tout est regroupé dans une interface simple,
        pensée pour les élèves, les familles et les enseignants.
      </p>
      <div class="public-cta-group">
        <a href="login.html" class="btn btn-primary">
          Se connecter
        </a>
      </div>
    </div>
  </section>

  <!-- FONCTIONNALITÉS PRINCIPALES -->
  <section class="public-section" id="features">
    <h2 class="public-section__title">Tout ce qu’il faut pour suivre la scolarité</h2>
    <p class="public-section__subtitle">
      ScoliOse centralise les informations essentielles pour les élèves et leurs responsables.
    </p>

    <div class="public-features">
      <article class="feature-card">
        <h3><i class="fa-solid fa-book-open"></i> Devoirs à faire</h3>
        <p>
          Visualisez les devoirs à venir, les consignes et les dates limites.
        </p>
      </article>

      <article class="feature-card">
        <h3><i class="fa-regular fa-calendar-days"></i> Emploi du temps</h3>
        <p>
          Consultez votre emploi du temps.
        </p>
      </article>

      <article class="feature-card">
        <h3><i class="fa-regular fa-message"></i> Messagerie</h3>
        <p>
          Échangez facilement avec les enseignants et l’équipe pédagogique.
        </p>
      </article>

      <article class="feature-card">
        <h3><i class="fa-solid fa-chart-line"></i> Suivi des notes</h3>
        <p>
          Suivez l’évolution des résultats et identifiez les matières à renforcer,
          avec une vue globale sur l’année.
        </p>
      </article>
    </div>
  </section>

  <!-- POUR QUI ? -->
  <section class="public-section public-section--light" id="pour-qui">
    <h2 class="public-section__title">Pour qui est fait ScoliOse ?</h2>

    <div class="public-audience">
      <article class="profile-card public-audience__card">
        <h3><i class="fa-solid fa-user-graduate"></i> Pour les élèves</h3>
        <p>
          Un espace pour consulter ses devoirs, suivre ses notes et
          communiquer avec les enseignants.
        </p>
      </article>

      <article class="profile-card public-audience__card">
        <h3><i class="fa-solid fa-user-group"></i> Pour les familles</h3>
        <p>
          Une seule application pour suivre la scolarité, être informé des résultats
          et des messages importants.
        </p>
      </article>

      <article class="profile-card public-audience__card">
        <h3><i class="fa-solid fa-chalkboard-user"></i> Pour les enseignants</h3>
        <p>
          Un outil simple pour partager devoirs, ressources, informations...
        </p>
      </article>
    </div>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
