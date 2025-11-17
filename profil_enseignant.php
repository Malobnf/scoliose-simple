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

$sqlTeacher = "
    SELECT u.email,
           tp.first_name,
           tp.last_name,
           tp.subject
    FROM users u
    LEFT JOIN teacher_profiles tp ON tp.user_id = u.id
    WHERE u.id = :uid
";
$stmt = $pdo->prepare($sqlTeacher);
$stmt->execute([':uid' => $teacherId]);
$teacher = $stmt->fetch();

$teacherName = trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? ''));
if ($teacherName === '') {
    $teacherName = 'Enseignant';
}
$teacherSubject = $teacher['subject'] ?? "Matière non renseignée";

// Classes de l'enseignant
$sqlClasses = "
    SELECT c.id, c.name, c.level
    FROM classes c
    JOIN teacher_classes tc ON tc.class_id = c.id
    WHERE tc.teacher_id = :tid
    ORDER BY c.name
";
$stmt = $pdo->prepare($sqlClasses);
$stmt->execute([':tid' => $teacherId]);
$classes = $stmt->fetchAll();

// Sélectionner une classe
$currentClassId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
$currentClass   = null;
$students       = [];

if ($currentClassId) {
    foreach ($classes as $c) {
        if ((int)$c['id'] === $currentClassId) {
            $currentClass = $c;
            break;
        }
    }

    if ($currentClass) {
        $sqlStudents = "
            SELECT u.id, sp.first_name, sp.last_name
            FROM users u
            JOIN student_profiles sp ON sp.user_id = u.id
            JOIN class_students cs ON cs.student_id = u.id
            WHERE cs.class_id = :cid
            ORDER BY sp.last_name, sp.first_name
        ";
        $stmt = $pdo->prepare($sqlStudents);
        $stmt->execute([':cid' => $currentClassId]);
        $students = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ScoliOse – Profil enseignant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="Js/app.js" defer></script>
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
    <a href="profil_enseignant.php" class="mobile-menu_link">Profil</a>
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


<main class="profile-main">
  <section class="hero hero--profile">
    <div class="hero-text">
      <h1><?= htmlspecialchars($teacherName) ?></h1>
      <h2>Enseignant – <?= htmlspecialchars($teacherSubject) ?></h2>
      <p class="hero-subtitle">
        Consultez vos classes, la liste des élèves, ajoutez des ressources et des notes.
      </p>
    </div>
  </section>

  <section class="profile-layout">
    <section class="profile-column profile-column--left">
      <article class="profile-card">
        <h2>Mes classes</h2>

        <?php if (empty($classes)): ?>
          <p>Aucune classe associée pour le moment.</p>
        <?php else: ?>
          <ul class="class-list">
            <?php foreach ($classes as $c): ?>
              <li class="class-item<?= $currentClassId === (int)$c['id'] ? ' class-item--active' : '' ?>">
                <a href="profil_enseignant.php?class_id=<?= (int)$c['id'] ?>" class="class-item__link">
                  <span class="class-item__name"><?= htmlspecialchars($c['name']) ?></span>
                  <?php if (!empty($c['level'])): ?>
                    <span class="class-item__level"><?= htmlspecialchars($c['level']) ?></span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </article>
    </section>

    <section class="profile-column profile-column--right">
      <article class="profile-card">
        <h2>
          <?php if ($currentClass): ?>
            Élèves de la classe <?= htmlspecialchars($currentClass['name']) ?>
          <?php else: ?>
            Sélectionnez une classe
          <?php endif; ?>
        </h2>

        <?php if ($currentClass && empty($students)): ?>
          <p>Aucun élève dans cette classe.</p>
        <?php elseif ($currentClass): ?>
          <table class="table">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Prénom</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['last_name']) ?></td>
                <td><?= htmlspecialchars($s['first_name']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <div class="teacher-actions">
            <a href="ajouter_ressource.php?class_id=<?= (int)$currentClassId ?>" class="btn btn-primary">
              Ajouter des ressources
            </a>
            <a href="ajouter_notes.php?class_id=<?= (int)$currentClassId ?>" class="btn btn-secondary">
              Ajouter des notes
            </a>
          </div>
        <?php else: ?>
          <p>Choisissez une classe dans la colonne de gauche pour voir la liste des élèves.</p>
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
