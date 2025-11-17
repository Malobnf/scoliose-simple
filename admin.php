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

$sqlRole = "
  SELECT r.name
  FROM users u
  JOIN roles r ON r.id = u.role_id
  WHERE u.id = :uid
";
$stmt = $pdo->prepare($sqlRole);
$stmt->execute([':uid' => $_SESSION['user_id']]);
$currentUserRole = $stmt->fetchColumn();

if ($currentUserRole !== 'ROLE_ADMIN') {
  http_response_code(403);
  echo "Accès interdit : cette page est réservée aux administrateurs.";
  exit;
}

// Rôles pour la création d’utilisateur
$stmt = $pdo->query("SELECT id, name, label FROM roles ORDER BY label ASC"); // ASC pour ascending (par ordre alphabétique)
$roles = $stmt->fetchAll();

$errorsUser = [];
$successUser = null;

$errorsClass = [];
$successClass = null;

// Formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formType = $_POST['form_type'] ?? '';

// Utilisateur
  if ($formType === 'create_user') {
    $email        = trim($_POST['email'] ?? '');
    $roleId       = (int)($_POST['role_id'] ?? 0);
    $tempPassword = $_POST['temp_password'] ?? '';
    $firstName    = trim($_POST['first_name'] ?? '');
    $lastName     = trim($_POST['last_name'] ?? '');
    $className    = trim($_POST['class_name'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errorsUser[] = "Adresse e-mail invalide.";
    }

    if ($roleId <= 0) {
      $errorsUser[] = "Veuillez sélectionner un rôle.";
    }

    if (strlen($tempPassword) < 8) {
      $errorsUser[] = "Le mot de passe temporaire doit contenir au moins 8 caractères.";
    }

    if ($firstName === '' || $lastName === '') {
      $errorsUser[] = "Prénom et nom sont obligatoires.";
    }

    $selectedRole = null;
    foreach ($roles as $r) {
      if ((int)$r['id'] === $roleId) {
        $selectedRole = $r;
        break;
      }
    }
    if (!$selectedRole) {
      $errorsUser[] = "Rôle sélectionné invalide.";
    }

    if (empty($errorsUser)) {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
      $stmt->execute([':email' => $email]);
      $count = (int)$stmt->fetchColumn();
      if ($count > 0) {
          $errorsUser[] = "Un utilisateur avec cet e-mail existe déjà.";
      }
    }

    if (empty($errorsUser)) {
      try {
        $pdo->beginTransaction();

        $passwordHash = password_hash($tempPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
          INSERT INTO users (email, password_hash, role_id, is_active, created_at, updated_at)
          VALUES (:email, :password_hash, :role_id, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
          ':email'         => $email,
          ':password_hash' => $passwordHash,
          ':role_id'       => $roleId,
        ]);
        $newUserId = (int)$pdo->lastInsertId();

      // Profil élève si rôle élève
    if ($selectedRole['name'] === 'ROLE_STUDENT') {
      $stmt = $pdo->prepare("
        INSERT INTO student_profiles (user_id, first_name, last_name, class_name, avatar_url)
        VALUES (:user_id, :first_name, :last_name, :class_name, :avatar_url)
      ");
      $stmt->execute([
        ':user_id'    => $newUserId,
        ':first_name' => $firstName,
        ':last_name'  => $lastName,
        ':class_name' => $className,
        ':avatar_url' => 'Images/avatar-1577909.svg',
      ]);
  }

  $pdo->commit();

  $successUser = "Utilisateur créé avec succès. 
  Communiquez-lui ce mot de passe temporaire : « " . htmlspecialchars($tempPassword) . " »";

  $email = $firstName = $lastName = $className = '';
  $roleId = 0;
  $tempPassword = '';

} catch (Exception $e) {
  $pdo->rollBack();
  $errorsUser[] = "Erreur lors de la création de l'utilisateur.";
    }
  }
}

// Classe
  if ($formType === 'create_class') {
    $level = $_POST['level'] ?? '';
    $section = trim($_POST['section'] ?? '');

    // Validation
    $allowedLevels = ['6ème', '5ème', '4ème', '3ème'];
    if (!in_array($level, $allowedLevels, true)) {
      $errorsClass[] = "Niveau invalide.";
  }

    if ($section === '' || !preg_match('/^[A-F]$/i', $section)) {
      $errorsClass[] = "La section doit être une lettre entre A et F.";
    }

    if (empty($errorsClass)) {
      $section = strtoupper($section);
      $className = $level . ' ' . $section;

      // Vérifier si existe déjà ou non
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE name = :name");
      $stmt->execute([':name' => $className]);
      $count = (int)$stmt->fetchColumn();

      if ($count > 0) {
        $errorsClass[] = "La classe « $className » existe déjà.";
      } else {
        $stmt = $pdo->prepare("
            INSERT INTO classes (name, level)
            VALUES (:name, :level)
          ");
        $stmt->execute([
          ':name'  => $className,
          ':level' => $level,
        ]);
        $successClass = "Classe « $className » créée avec succès.";
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Administration</title>
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

  <h1 class="site-title">ScoliOse – Admin</h1>

  <nav class="mobile-menu" id="mobile-menu">
    <a href="accueil.php" class="mobile-menu_link">Accueil</a>
    <a href="messagerie.php" class="mobile-menu_link">Messagerie</a>
    <a href="profil.php" class="mobile-menu_link">Profil</a>
    <a href="admin.php" class="mobile-menu_link">Administration</a>
    <a href="logout.php" class="mobile-menu_link">Déconnexion</a>
  </nav>
</header>

<main class="profile-main">
  <section class="hero hero--profile">
    <div class="hero-text">
      <h1>Administration</h1>
      <h2>Gestion des utilisateurs et des classes</h2>
    </div>
  </section>

  <section class="profile-layout">
    <!-- COLONNE GAUCHE : CRÉATION D'UTILISATEUR -->
    <section class="profile-column">
      <article class="profile-card">
        <h2>Créer un utilisateur</h2>

        <?php if (!empty($errorsUser)): ?>
          <div style="margin-bottom:8px; padding:8px; border-radius:8px; background:#fee2e2; color:#991b1b; font-size:0.85rem;">
            <ul style="margin:0; padding-left:18px;">
              <?php foreach ($errorsUser as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($successUser): ?>
          <div style="margin-bottom:8px; padding:8px; border-radius:8px; background:#dcfce7; color:#166534; font-size:0.85rem;">
            <?= $successUser ?>
          </div>
        <?php endif; ?>

        <form method="post" action="admin.php" class="auth-form">
          <input type="hidden" name="form_type" value="create_user">

          <div class="auth-field">
            <label for="email">Adresse e-mail (nom d’utilisateur)</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-regular fa-envelope"></i></span>
              <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
              >
            </div>
          </div>

          <div class="auth-field">
            <label for="role_id">Rôle</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-solid fa-user-shield"></i></span>
              <select id="role_id" name="role_id" required style="border:none; background:transparent; padding:8px 10px; flex:1;">
                <option value="">-- Sélectionner un rôle --</option>
                <?php foreach ($roles as $r): ?>
                  <option value="<?= (int)$r['id'] ?>" <?= (isset($roleId) && (int)$r['id'] === (int)$roleId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['label']) ?> (<?= htmlspecialchars($r['name']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="auth-field">
            <label for="temp_password">Mot de passe temporaire</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-solid fa-key"></i></span>
              <input
                type="text"
                id="temp_password"
                name="temp_password"
                required
                minlength="8"
                placeholder="Mot de passe provisoire"
                value="<?= isset($tempPassword) ? htmlspecialchars($tempPassword) : '' ?>"
              >
            </div>
          </div>

          <div class="auth-field">
            <label for="first_name">Prénom</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-regular fa-id-badge"></i></span>
              <input
                type="text"
                id="first_name"
                name="first_name"
                required
                value="<?= isset($firstName) ? htmlspecialchars($firstName) : '' ?>"
              >
            </div>
          </div>

          <div class="auth-field">
            <label for="last_name">Nom</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-regular fa-id-badge"></i></span>
              <input
                type="text"
                id="last_name"
                name="last_name"
                required
                value="<?= isset($lastName) ? htmlspecialchars($lastName) : '' ?>"
              >
            </div>
          </div>

          <div class="auth-field">
            <label for="class_name">Classe (pour un élève)</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-solid fa-users"></i></span>
              <input
                type="text"
                id="class_name"
                name="class_name"
                placeholder="ex : 4ème B"
                value="<?= isset($className) ? htmlspecialchars($className) : '' ?>"
              >
            </div>
          </div>

          <button type="submit" class="btn btn-primary auth-submit" style="margin-top:10px;">
            <span>Créer l’utilisateur</span>
            <i class="fa-solid fa-user-plus"></i>
          </button>
        </form>
      </article>
    </section>

    <section class="profile-column">
      <article class="profile-card">
        <h2>Créer une classe</h2>

        <?php if (!empty($errorsClass)): ?>
          <div style="margin-bottom:8px; padding:8px; border-radius:8px; background:#fee2e2; color:#991b1b; font-size:0.85rem;">
            <ul style="margin:0; padding-left:18px;">
              <?php foreach ($errorsClass as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($successClass): ?>
          <div style="margin-bottom:8px; padding:8px; border-radius:8px; background:#dcfce7; color:#166534; font-size:0.85rem;">
            <?= htmlspecialchars($successClass) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="admin.php" class="auth-form">
          <input type="hidden" name="form_type" value="create_class">

          <div class="auth-field">
            <label for="level">Niveau</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-solid fa-layer-group"></i></span>
              <select id="level" name="level" required>
                <option value="">-- Sélectionner un niveau --</option>
                <option value="6ème">6ème</option>
                <option value="5ème">5ème</option>
                <option value="4ème">4ème</option>
                <option value="3ème">3ème</option>
              </select>
            </div>
          </div>

          <div class="auth-field">
            <label for="section">Section (A à F)</label>
            <div class="auth-input-wrapper">
              <span class="auth-input-icon"><i class="fa-solid fa-font"></i></span>
              <input
                type="text"
                id="section"
                name="section"
                maxlength="1"
                placeholder="ex : A"
                required
              >
            </div>
          </div>

          <button type="submit" class="btn btn-secondary auth-submit" style="margin-top:10px;">
            <span>Créer la classe</span>
            <i class="fa-solid fa-school"></i>
          </button>
        </form>
      </article>
    </section>
  </section>
</main>

<footer class="footer">
  <p>&copy; 2025 - ScoliOse</p>
</footer>
</body>
</html>
