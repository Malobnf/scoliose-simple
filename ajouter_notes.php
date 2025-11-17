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

$errors = [];
$success = null;

// Chercher dans bdd les classes de l'enseignant connecté
$sqlClasses = "
    SELECT c.id, c.name
    FROM classes c
    JOIN teacher_classes tc ON tc.class_id = c.id
    WHERE tc.teacher_id = :tid
    ORDER BY c.name
";
$stmt = $pdo->prepare($sqlClasses);
$stmt->execute([':tid' => $teacherId]);
$classes = $stmt->fetchAll();

$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = (int)($_POST['class_id'] ?? 0);
    $scaleMax = (int)($_POST['scale_max'] ?? 20);
    $grades   = $_POST['grade'] ?? [];

    if ($classId <= 0) $errors[] = "Classe invalide.";
    if ($scaleMax <= 0) $scaleMax = 20;

    if (empty($errors)) {
        $success = "Les notes ont été enregistrées.";
    }
}

// Afficher les élèves de la classe sélectionnée
$students = [];
if ($classId > 0) {
    $sqlStudents = "
        SELECT u.id, sp.first_name, sp.last_name
        FROM users u
        JOIN student_profiles sp ON sp.user_id = u.id
        JOIN class_students cs ON cs.student_id = u.id
        WHERE cs.class_id = :cid
        ORDER BY sp.last_name, sp.first_name
    ";
    $stmt = $pdo->prepare($sqlStudents);
    $stmt->execute([':cid' => $classId]);
    $students = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Ajouter des notes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
  <script src="Js/notifications.js" defer></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const header = document.getElementById('notes-header');
      const scaleInput = document.getElementById('scale-max');

      if (!header || !scaleInput) return;

      const scales = [20, 10, 5];
      let index = scales.indexOf(parseInt(scaleInput.value, 10));
      if (index === -1) index = 0;

      const updateHeader = () => {
        header.textContent = 'Notes (/' + scales[index] + ')';
        scaleInput.value = scales[index];
      };

      header.addEventListener('click', () => {
        index = (index + 1) % scales.length;
        updateHeader();
      });

      updateHeader();
    });
  </script>
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
        <h2>Ajouter des notes</h2>

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

        <form method="get" action="ajouter_notes.php">
          <div class="auth-field">
            <label for="class_id">Choisir une classe</label>
            <select id="class_id" name="class_id" onchange="this.form.submit()">
              <option value="0">-- Sélectionner --</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= $classId === (int)$c['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>
      </article>
    </section>

    <section class="profile-column profile-column--right">
      <article class="profile-card">
        <h2>Notes de la classe</h2>

        <?php if ($classId > 0 && empty($students)): ?>
          <p>Aucun élève dans cette classe.</p>
        <?php elseif ($classId > 0): ?>
          <form method="post" action="ajouter_notes.php">
            <input type="hidden" name="class_id" value="<?= (int)$classId ?>">
            <input type="hidden" id="scale-max" name="scale_max" value="20">

            <table class="table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Prénom</th>
                  <th id="notes-header" style="cursor:pointer;">Notes (/20)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($students as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['last_name']) ?></td>
                    <td><?= htmlspecialchars($s['first_name']) ?></td>
                    <td>
                      <input 
                        type="number" 
                        step="0.1" 
                        name="grade[<?= (int)$s['id'] ?>]"
                        min="0" max="20"
                        class="grade-input"
                      >
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <button type="submit" class="btn btn-primary" style="margin-top:10px;">
              Enregistrer les notes
            </button>
            <p style="font-size:0.8rem;color:#6b7280;margin-top:4px;">
              Cliquez sur l’en-tête "Notes" pour changer l’échelle (20 → 10 → 5).
            </p>
          </form>
        <?php else: ?>
          <p>Sélectionnez d’abord une classe pour afficher les élèves.</p>
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
