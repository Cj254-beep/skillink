<?php
$title = 'Home';
require 'includes/header.php';
?>
<section class="intro">
  <h1>Find an attachment that fits what you can do.</h1>
  <p>Students list their skills. Employers post openings. SkillLink shows each student the openings that match them best.</p>
  <?php if (!$user): ?>
    <p><a class="btn" href="register.php">Create account</a> <a class="btn ghost" href="login.php">Log in</a></p>
  <?php elseif ($user['role'] === 'student'): ?>
    <p><a class="btn" href="openings.php">See openings for you</a></p>
  <?php else: ?>
    <p><a class="btn" href="post_opening.php">Post an opening</a></p>
  <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
