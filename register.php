<?php
require_once 'includes/config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? '';

    if ($name === '') $errors[] = 'Enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 6) $errors[] = 'Use a password of at least 6 characters.';
    if (!in_array($role, ['student', 'employer'], true)) $errors[] = 'Choose student or employer.';

    if (!$errors) {
        $q = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $q->execute([$email]);
        if ($q->fetch()) {
            $errors[] = 'That email is already registered. Log in instead.';
        } else {
            $ins = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)');
            $ins->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
            flash('Account created. You can now log in.');
            header('Location: login.php');
            exit;
        }
    }
}
$title = 'Create account';
require 'includes/header.php';
?>
<h1>Create account</h1>
<?php foreach ($errors as $err): ?><p class="error"><?= e($err) ?></p><?php endforeach; ?>
<form method="post" class="card">
  <label>Full name <input name="name" value="<?= e($_POST['name'] ?? '') ?>" required></label>
  <label>Email <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label>I am a
    <select name="role">
      <option value="student">Student looking for attachment</option>
      <option value="employer">Employer posting openings</option>
    </select>
  </label>
  <button class="btn">Create account</button>
</form>
<?php require 'includes/footer.php'; ?>
