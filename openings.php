<?php
require_once 'includes/config.php';
require_role('student');
$uid = $_SESSION['user']['id'];

$q = $pdo->prepare('SELECT skills FROM student_profiles WHERE user_id = ?');
$q->execute([$uid]);
$profile = $q->fetch();
$mySkills = $profile ? skills_array($profile['skills']) : [];

$q = $pdo->prepare('SELECT o.*, u.name AS employer,
                    (SELECT COUNT(*) FROM applications a WHERE a.opening_id = o.id AND a.student_id = ?) AS applied
                    FROM openings o JOIN users u ON u.id = o.employer_id
                    WHERE o.closing_date >= CURDATE()');
$q->execute([$uid]);
$list = $q->fetchAll();

// Simple matching rule: count how many required skills the student has
foreach ($list as &$o) {
    $need = skills_array($o['required_skills']);
    $o['matched'] = array_values(array_intersect($need, $mySkills));
    $o['total'] = count($need);
}
unset($o);
usort($list, fn($a, $b) => count($b['matched']) <=> count($a['matched']));

$title = 'Openings';
require 'includes/header.php';
?>
<h1>Openings for you</h1>
<?php if (!$profile): ?>
  <p class="notice">Add your skills in <a href="profile.php">your profile</a> to see which openings match you best.</p>
<?php endif; ?>
<?php if (!$list): ?>
  <p class="hint">No openings are open right now. Check back soon.</p>
<?php endif; ?>
<?php foreach ($list as $o): ?>
  <article class="item">
    <div class="match">
      <strong><?= count($o['matched']) ?> of <?= (int)$o['total'] ?></strong> skills match
      <span class="meter"><i style="width: <?= $o['total'] ? round(100 * count($o['matched']) / $o['total']) : 0 ?>%"></i></span>
    </div>
    <h3><?= e($o['title']) ?></h3>
    <p class="hint"><?= e($o['employer']) ?> &middot; closes <?= e($o['closing_date']) ?> &middot; <?= (int)$o['positions'] ?> position(s)</p>
    <p><?= e($o['description']) ?></p>
    <p class="hint">Skills needed: <?= e($o['required_skills']) ?></p>
    <?php if ($o['applied']): ?>
      <p class="done">You have applied</p>
    <?php else: ?>
      <form method="post" action="apply.php">
        <input type="hidden" name="opening_id" value="<?= (int)$o['id'] ?>">
        <button class="btn">Apply</button>
      </form>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
