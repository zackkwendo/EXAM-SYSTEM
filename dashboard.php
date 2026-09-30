<?php
require 'config.php'; require_trainee();
$uid=$_SESSION['user']['id'];
$mods=db()->query("SELECT * FROM modules ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$rows=[];
foreach($mods as $m){
 $s=db()->prepare("SELECT * FROM attempts WHERE user_id=? AND module_id=? ORDER BY id DESC LIMIT 1"); $s->execute([$uid,$m['id']]); $a=$s->fetch(PDO::FETCH_ASSOC);
 $rows[]=['m'=>$m,'a'=>$a];
}
?>
<!doctype html><html><head><?php include 'partials/head.php'; ?></head><body><?php include 'partials/nav.php'; ?>
<main class="container"><h1>Welcome, <?=e($_SESSION['user']['name'])?></h1><p class="muted">Complete each module in order. A pass unlocks the next module.</p>
<?php show_flash(); ?>
<div class="grid">
<?php foreach($rows as $i=>$r): $m=$r['m']; $a=$r['a']; $unlocked=($i===0); if($i>0){$prev=$rows[$i-1]['a']; $unlocked=$prev && $prev['status']==='passed';} ?>
<div class="card module-card">
<div class="module-num">MODULE <?=$m['id']?></div><h2><?=e($m['title'])?></h2><p><?=e($m['description'])?></p>
<?php if($a && $a['status']==='passed'): ?><div class="status pass">Passed — <?=number_format((float)$a['score'],1)?>%</div><a class="btn secondary" href="result.php?id=<?=$a['id']?>">View result</a>
<?php elseif($a && $a['status']==='failed'): ?><div class="status fail">Not passed — <?=number_format((float)$a['score'],1)?>%</div><a class="btn primary" href="exam.php?module=<?=$m['id']?>">Retake</a>
<?php elseif($unlocked): ?><div class="status ready">Available</div><a class="btn primary" href="exam.php?module=<?=$m['id']?>">Start assessment</a>
<?php else: ?><div class="status locked">Locked</div><?php endif; ?>
</div>
<?php endforeach; ?>
</div></main></body></html>