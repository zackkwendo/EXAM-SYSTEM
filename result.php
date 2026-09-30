<?php
require 'config.php'; require_trainee();
$id=(int)($_GET['id']??0);
$s=db()->prepare("SELECT a.*,m.title,m.pass_mark,u.name FROM attempts a JOIN modules m ON m.id=a.module_id JOIN users u ON u.id=a.user_id WHERE a.id=? AND a.user_id=?");
$s->execute([$id,$_SESSION['user']['id']]);$a=$s->fetch(PDO::FETCH_ASSOC); if(!$a) exit('Result not found');
?>
<!doctype html><html><head><?php include 'partials/head.php'; ?></head><body><?php include 'partials/nav.php'; ?>
<main class="container narrow"><div class="card result">
<div class="module-num"><?=e($a['title'])?></div><h1>Assessment Result</h1>
<div class="big-score"><?=number_format((float)$a['score'],1)?>%</div>
<div class="status <?=$a['status']==='passed'?'pass':'fail'?>"><?=strtoupper($a['status'])?> — Grade <?=grade((float)$a['score'])?></div>
<p>Pass mark: <?=$a['pass_mark']?>%</p>
<?php if($a['status']==='passed'): ?><p>Congratulations. The next module is now unlocked.</p><a class="btn primary" href="dashboard.php">Continue</a>
<?php else: ?><p>You may retake this module from your dashboard.</p><a class="btn primary" href="dashboard.php">Back to dashboard</a><?php endif; ?>
</div></main></body></html>