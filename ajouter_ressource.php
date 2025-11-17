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
if (($_SESSION['role_name'] ?? '') !== 'ROLE_TEACHER') {
    http_response_code(403);
    echo "Accès réservé aux enseignants.";
    exit;
}

$teacherId = (int) $_SESSION['user_id'];
$classId   = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = (int)($_POST['class_id'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $type    = trim($_POST['type'] ?? '');

    if ($classId <= 0) $errors[] = "Classe invalide.";
    if ($title === '') $errors[] = "Le titre est obligatoire.";
    if ($type === '') $errors[] = "Le type de ressource est obligatoire.";

    $filePath = null;
    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $basename = basename($_FILES['file']['name']);
            $targetName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $basename);
            $targetPath = $uploadDir . '/' . $targetName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $filePath = 'uploads/' . $targetName;
            } else {
                $errors[] = "Erreur lors de l'envoi du fichier.";
            }
        } else {
            $errors[] = "Erreur d'upload (code " . $_FILES['file']['error'] . ").";
        }
    }

    if (empty($errors)) {
        $sql = "
            INSERT INTO resources (class_id, teacher_id, title, type, file_path)
            VALUES (:class_id, :teacher_id, :title, :type, :file_path)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':class_id'   => $classId,
            ':teacher_id' => $teacherId,
            ':title'      => $title,
            ':type'       => $type,
            ':file_path'  => $filePath,
        ]);
        $success = "Ressource ajoutée avec succès.";
        $title = $type = '';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Ajouter une ressource</title>
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
    <section class="profile-column profile-column--left">
      <article class="profile-card">
        <h2>Ajouter une ressource</h2>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <ul>
              <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="ajouter_ressource.php" enctype="multipart/form-data">
          <input type="hidden" name="class_id" value="<?= (int)$classId ?>">

          <div class="auth-field">
            <label for="title">Titre de la ressource</label>
            <input type="text" id="title" name="title" required value="<?= htmlspecialchars($title ?? '') ?>">
          </div>

          <div class="auth-field">
            <label for="type">Type de ressource</label>
            <select id="type" name="type" required>
              <option value="">-- Choisir --</option>
              <option value="Document" <?= (isset($type) && $type === 'Document') ? 'selected' : '' ?>>Document</option>
              <option value="Vidéo" <?= (isset($type) && $type === 'Vidéo') ? 'selected' : '' ?>>Vidéo</option>
              <option value="Lien" <?= (isset($type) && $type === 'Lien') ? 'selected' : '' ?>>Lien</option>
              <option value="Autre" <?= (isset($type) && $type === 'Autre') ? 'selected' : '' ?>>Autre</option>
            </select>
          </div>

          <div class="auth-field">
            <label for="file">Fichier</label>
            <input type="file" id="file" name="file">
            <small>(optionnel)</small>
          </div>

          <button type="submit" class="btn btn-primary" style="margin-top:10px;">
            Enregistrer la ressource
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
