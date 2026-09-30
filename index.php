<?php
require 'config.php';
if (!empty($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role']==='admin' ? 'admin.php' : 'dashboard.php')); exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare("SELECT * FROM users WHERE username=? AND active=1");
    $stmt->execute([$username]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];
        header('Location: '.($u['role']==='admin'?'admin.php':'dashboard.php')); exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html><html><head><?php include 'partials/head.php'; ?></head><body>
<div class="login-wrap"><div class="card login-card">
<div class="brand">NYOTA ICT</div><h1>Training Assessment</h1>
<p class="muted">Online assessment portal for the NYOTA ICT trainees.</p>
<?php if($error): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
<form method="post">
<label>Username</label><input name="username" required autofocus>
<label>Password</label><input type="password" name="password" required>
<button class="btn primary full">Sign in</button>
</form>
<div class="login-note">Trainees: use your assigned account. Trainer: use the administrator account.</div>
</div></div>
</body></html>