<?php
require __DIR__ . '/inc/db.php';
session_start();

$error = null;
$email = ''; // Garder le mail affiché si erreur de mdp par exemple

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $email = trim($email);

    // Récupérer utilisateur + rôle
    $stmt = $pdo->prepare('
        SELECT u.id, u.email, u.password_hash, u.role_id, r.name AS role_name
        FROM users u
        JOIN roles r ON r.id = u.role_id
        WHERE u.email = :email
    ');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['role_id']   = (int)$user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];

    if ($user['role_name'] === 'ROLE_ADMIN') {
      header('Location: admin.php');
    } elseif ($user['role_name'] === 'ROLE_TEACHER') {
      header('Location: profil_enseignant.php');
    } elseif ($user['role_name'] === 'ROLE_STUDENT') {
      header('Location: accueil.php');
    } else {
      header('Location: index.php');
    }
    exit;

    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Connexion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
</head>

<body>
<header class="site-header">
  <button class="burger-btn" id="burger-btn" aria-label="Ouvrir le menu">
    <i class="fa-solid fa-bars"></i>
  </button>

  <h1 class="site-title">ScoliOse</h1>

  <nav class="mobile-menu" id="mobile-menu">
    <a href="index.php" class="mobile-menu_link">Accueil</a>
    <a href="logout.php" class="mobile-menu_link">Déconnexion</a>
  </nav>
</header>

<main class="auth-main">
  <section class="auth-layout">
    <section class="auth-card">
      <h2 class="auth-title">Connexion</h2>
      <p class="auth-subtitle">
        Connectez-vous pour accéder à vos messages, vos notes et vos ressources.
      </p>

      <form class="auth-form" method="post" action="login.php">
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
              placeholder="admin@example.com"
              value="<?= htmlspecialchars($email) ?>"
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

        <?php if ($error): ?>
          <p class="auth-error" id="login-error" aria-live="polite">
            <?= htmlspecialchars($error) ?>
          </p>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary auth-submit">
          <span>Se connecter</span>
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
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
