<?php
require_once 'includes/config.php';
require_role('student');
$uid = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = (int)($_POST['opening_id'] ?? 0);

    $p = $pdo->prepare('SELECT 1 FROM student_profiles WHERE user_id = ?');
    $p->execute([$uid]);
    if (!$p->fetch()) {
        flash('Complete your profile before you apply.');
        header('Location: profile.php');
        exit;
    }

    $o = $pdo->prepare('SELECT id FROM openings WHERE id = ? AND closing_date >= CURDATE()');
    $o->execute([$oid]);
    if (!$o->fetch()) {
        flash('That opening is closed or does not exist.');
    } else {
        $ins = $pdo->prepare('INSERT IGNORE INTO applications (opening_id, student_id) VALUES (?,?)');
        $ins->execute([$oid, $uid]);
        flash($ins->rowCount() ? 'Application submitted.' : 'You have already applied to this opening.');
    }
}
header('Location: openings.php');
