<nav class="topbar"><div><strong>NYOTA ICT Assessment</strong></div><div class="navlinks">
<?php if(!empty($_SESSION['user'])): ?>
<span><?=e($_SESSION['user']['name'])?></span>
<a href="<?= $_SESSION['user']['role']==='admin'?'admin.php':'dashboard.php' ?>">Dashboard</a>
<a href="logout.php">Logout</a>
<?php endif; ?></div></nav>