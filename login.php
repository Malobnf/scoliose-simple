<?php
// login.php – page de connexion
require __DIR__ . '/inc/db.php';

// TODO plus tard : traiter $_POST pour vérifier l'identifiant / mot de passe
// et rediriger vers index.php en cas de succès.
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Connexion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<header class="site-header site-header--center">
  <h1 class="site-title">ScoliOse</h1>
</header>

<main class="auth-main">
  <section class="auth-layout">
    <!-- Colonne gauche : formulaire -->
    <section class="auth-card">
      <h2 class="auth-title">Connexion</h2>
      <p class="auth-subtitle">
        Connectez-vous pour accéder à votre tableau de bord, vos messages et vos résultats.
      </p>

      <!-- Formulaire de login -->
      <form class="auth-form" method="post" action="/login">
        <div class="auth-field">
          <label for="login-email">Adresse e-mail</label>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon">
              <i class="fa-regular fa-envelope"></i>
            </span>
            <input
              type="email"
              id="login-email"
              name="email"
              required
              autocomplete="email"
              placeholder="prenom.nom@example.com"
            >
          </div>
        </div>

        <div class="auth-field">
          <label for="login-password">Mot de passe</label>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon">
              <i class="fa-solid fa-lock"></i>
            </span>
            <input
              type="password"
              id="login-password"
              name="password"
              required
              autocomplete="current-password"
              placeholder="Votre mot de passe"
            >
          </div>
        </div>

        <div class="auth-options">
          <label class="auth-remember">
            <input type="checkbox" name="remember_me">
            <span>Se souvenir de moi</span>
          </label>
          <a href="#" class="auth-link">Mot de passe oublié ?</a>
        </div>

        <!-- Zone de message d’erreur -->
        <p class="auth-error" id="login-error" aria-live="polite" hidden>
          Identifiants incorrects. Merci de réessayer.
        </p>

        <button type="submit" class="btn btn-primary auth-submit">
          <span>Se connecter</span>
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
      </form>

      <p class="auth-footer-text">
        Besoin d’aide ? Contactez l’établissement scolaire.
      </p>
    </section>

    <!-- Colonne droite : illustration -->
    <section class="auth-side">
      <div class="auth-side-card">
        <h2>Un espace élève centralisé</h2>
        <p>
          Retrouvez vos devoirs, vos notes, votre messagerie et vos ressources pédagogiques
          au même endroit, dans une interface claire et accessible.
        </p>
        <ul class="auth-side-list">
          <li><i class="fa-solid fa-check"></i> Suivi des notes en temps réel</li>
          <li><i class="fa-solid fa-check"></i> Communication avec les enseignants</li>
          <li><i class="fa-solid fa-check"></i> Rappels pour les devoirs importants</li>
        </ul>
      </div>

      <div class="auth-illustration-wrapper">
        <img
          src="assets/images/login-illustration.svg"
          alt="Illustration élève connecté"
          class="auth-illustration"
        >
      </div>
    </section>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
