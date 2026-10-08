<?php
require_once 'includes/config.php';
require_role('student');
$q = $pdo->prepare('SELECT a.status, a.applied_at, o.title, u.name AS employer
                    FROM applications a
                    JOIN openings o ON o.id = a.opening_id
                    JOIN users u ON u.id = o.employer_id
                    WHERE a.student_id = ? ORDER BY a.applied_at DESC');
$q->execute([$_SESSION['user']['id']]);
$list = $q->fetchAll();
$title = 'My applications';
require 'includes/header.php';
?>
<h1>My applications</h1>
<?php if (!$list): ?>
  <p class="hint">You have not applied yet. <a href="openings.php">See openings for you</a>.</p>
<?php endif; ?>
<?php foreach ($list as $a): ?>
  <article class="item">
    <h3><?= e($a['title']) ?></h3>
    <p class="hint"><?= e($a['employer']) ?> &middot; applied <?= e(substr($a['applied_at'], 0, 10)) ?></p>
    <p><span class="status <?= e($a['status']) ?>"><?= e(ucfirst($a['status'])) ?></span></p>
  </article>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
