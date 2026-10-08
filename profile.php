<?php
require_once 'includes/config.php';
require_role('student');
$uid = $_SESSION['user']['id'];
$errors = [];

$q = $pdo->prepare('SELECT * FROM student_profiles WHERE user_id = ?');
$q->execute([$uid]);
$p = $q->fetch() ?: ['course' => '', 'year_of_study' => '', 'skills' => '', 'cv_path' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course'] ?? '');
    $year   = (int)($_POST['year_of_study'] ?? 0);
    $skills = trim($_POST['skills'] ?? '');
    $cv     = $p['cv_path'];

    if ($course === '') $errors[] = 'Enter your course.';
    if ($year < 1 || $year > 6) $errors[] = 'Enter your year of study (1 to 6).';
    if (count(skills_array($skills)) < 5) $errors[] = 'List at least five skills, separated by commas.';

    if (!empty($_FILES['cv']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'doc', 'docx'], true)) {
            $errors[] = 'Upload your CV as a PDF or Word file.';
        } elseif ($_FILES['cv']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Your CV must be smaller than 2 MB.';
        } elseif ($_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cv = 'cv_' . $uid . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . '/uploads/' . $cv);
        }
    }

    if (!$errors) {
        $pdo->prepare('REPLACE INTO student_profiles (user_id, course, year_of_study, skills, cv_path) VALUES (?,?,?,?,?)')
            ->execute([$uid, $course, $year, $skills, $cv]);
        flash('Profile saved.');
        header('Location: profile.php');
        exit;
    }
    $p = ['course' => $course, 'year_of_study' => $year, 'skills' => $skills, 'cv_path' => $cv];
}
$title = 'My profile';
require 'includes/header.php';
?>
<h1>My profile</h1>
<?php foreach ($errors as $err): ?><p class="error"><?= e($err) ?></p><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="card">
  <label>Course <input name="course" value="<?= e($p['course']) ?>" required></label>
  <label>Year of study <input type="number" name="year_of_study" min="1" max="6" value="<?= e($p['year_of_study']) ?>" required></label>
  <label>Skills (separate with commas, at least five)
    <textarea name="skills" rows="3" required><?= e($p['skills']) ?></textarea>
  </label>
  <label>CV (PDF or Word, up to 2 MB)
    <input type="file" name="cv" accept=".pdf,.doc,.docx">
  </label>
  <?php if ($p['cv_path']): ?><p class="hint">Current CV: <?= e($p['cv_path']) ?></p><?php endif; ?>
  <button class="btn">Save profile</button>
</form>
<?php require 'includes/footer.php'; ?>
