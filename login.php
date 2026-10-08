<?php
require_once 'includes/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $q->execute([strtolower(trim($_POST['email'] ?? ''))]);
    $u = $q->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'role' => $u['role']];
        header('Location: ' . ($u['role'] === 'student' ? 'openings.php' : 'post_opening.php'));
        exit;
    }
    $error = 'Email or password is incorrect. Check both and try again.';
}
$title = 'Log in';
require 'includes/header.php';
?>
<h1>Log in</h1>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="card">
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn">Log in</button>
</form>
<?php require 'includes/footer.php'; ?>
