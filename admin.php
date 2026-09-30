<?php
require 'config.php'; require_admin();
$mods=db()->query("SELECT * FROM modules ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$trainees=db()->query("SELECT * FROM users WHERE role='trainee' AND active=1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><?php include 'partials/head.php'; ?></head><body><?php include 'partials/nav.php'; ?>
<main class="container"><h1>Trainer Dashboard</h1><p class="muted">Four active trainees. The fifth trainee slot is intentionally unused.</p>
<div class="card"><h2>Trainee progress</h2><div class="table-wrap"><table><thead><tr><th>Trainee</th>
<?php foreach($mods as $m): ?><th>Month <?=$m['id']?></th><?php endforeach; ?><th>Overall</th></tr></thead><tbody>
<?php foreach($trainees as $t): ?><tr><td><strong><?=e($t['name'])?></strong></td>
<?php $sum=0;$n=0; foreach($mods as $m): $s=db()->prepare("SELECT score,status FROM attempts WHERE user_id=? AND module_id=? ORDER BY id DESC LIMIT 1");$s->execute([$t['id'],$m['id']]);$a=$s->fetch(PDO::FETCH_ASSOC); if($a){$sum+=(float)$a['score'];$n++;} ?>
<td><?= $a ? number_format((float)$a['score'],1).'%' : '<span class="muted">—</span>' ?></td><?php endforeach; ?>
<td><?= $n?number_format($sum/$n,1).'%' : '—' ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<div class="card"><h2>Assessment records</h2><div class="table-wrap"><table><thead><tr><th>Trainee</th><th>Module</th><th>Score</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php $r=db()->query("SELECT a.*,u.name,m.title FROM attempts a JOIN users u ON u.id=a.user_id JOIN modules m ON m.id=a.module_id ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
foreach($r as $x): ?><tr><td><?=e($x['name'])?></td><td><?=e($x['title'])?></td><td><?=number_format((float)$x['score'],1)?>%</td><td><?=e($x['status'])?></td><td><?=e(substr($x['submitted_at'],0,19))?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<div class="card"><h2>Accounts</h2><table><tr><th>Name</th><th>Username</th><th>Role</th></tr>
<?php foreach($trainees as $t): ?><tr><td><?=e($t['name'])?></td><td><?=e($t['username'])?></td><td>Trainee</td></tr><?php endforeach; ?></table>
<p class="muted">Default passwords are shown in setup.php only. Change them before live use.</p></div>
</main></body></html>