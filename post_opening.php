<?php
require_once 'includes/config.php';
require_role('employer');
$uid = $_SESSION['user']['id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = trim($_POST['title'] ?? '');
    $d = trim($_POST['description'] ?? '');
    $s = trim($_POST['required_skills'] ?? '');
    $n = (int)($_POST['positions'] ?? 0);
    $c = $_POST['closing_date'] ?? '';

    if ($t === '') $errors[] = 'Enter a title for the opening.';
    if ($d === '') $errors[] = 'Describe the opening.';
    if (!skills_array($s)) $errors[] = 'List the skills you need, separated by commas.';
    if ($n < 1) $errors[] = 'Enter at least one position.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $c) || $c < date('Y-m-d')) $errors[] = 'Choose a closing date that is today or later.';

    if (!$errors) {
        $pdo->prepare('INSERT INTO openings (employer_id, title, description, required_skills, positions, closing_date) VALUES (?,?,?,?,?,?)')
            ->execute([$uid, $t, $d, $s, $n, $c]);
        flash('Opening posted.');
        header('Location: post_opening.php');
        exit;
    }
}

$mine = $pdo->prepare('SELECT o.*, (SELECT COUNT(*) FROM applications a WHERE a.opening_id = o.id) AS applicants
                       FROM openings o WHERE employer_id = ? ORDER BY created_at DESC');
$mine->execute([$uid]);
$list = $mine->fetchAll();

$title = 'My openings';
require 'includes/header.php';
?>
<h1>Post an opening</h1>
<?php foreach ($errors as $err): ?><p class="error"><?= e($err) ?></p><?php endforeach; ?>
<form method="post" class="card">
  <label>Title <input name="title" value="<?= e($_POST['title'] ?? '') ?>" required></label>
  <label>Description <textarea name="description" rows="4" required><?= e($_POST['description'] ?? '') ?></textarea></label>
  <label>Skills needed (separate with commas) <input name="required_skills" value="<?= e($_POST['required_skills'] ?? '') ?>" required></label>
  <label>Positions available <input type="number" name="positions" min="1" value="<?= e($_POST['positions'] ?? 1) ?>" required></label>
  <label>Closing date <input type="date" name="closing_date" value="<?= e($_POST['closing_date'] ?? '') ?>" required></label>
  <button class="btn">Post opening</button>
</form>

<h2>Your openings</h2>
<?php if (!$list): ?>
  <p class="hint">You have not posted an opening yet. Use the form above to post your first one.</p>
<?php endif; ?>
<?php foreach ($list as $o): ?>
  <article class="item">
    <h3><?= e($o['title']) ?></h3>
    <p><?= e($o['description']) ?></p>
    <p class="hint">Skills: <?= e($o['required_skills']) ?> &middot; <?= (int)$o['positions'] ?> position(s) &middot; closes <?= e($o['closing_date']) ?> &middot; <?= (int)$o['applicants'] ?> applicant(s)</p>
  </article>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
