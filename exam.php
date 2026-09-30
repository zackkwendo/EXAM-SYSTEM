<?php
require 'config.php'; require_trainee();
$mid=(int)($_GET['module']??0);
$m=db()->prepare("SELECT * FROM modules WHERE id=?"); $m->execute([$mid]); $module=$m->fetch(PDO::FETCH_ASSOC);
if(!$module) exit('Module not found');
$uid=$_SESSION['user']['id'];
if($mid>1){
 $s=db()->prepare("SELECT status FROM attempts WHERE user_id=? AND module_id=? ORDER BY id DESC LIMIT 1"); $s->execute([$uid,$mid-1]); $prev=$s->fetchColumn();
 if($prev!=='passed'){header('Location: dashboard.php');exit;}
}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $answers=$_POST['answer']??[];
 $qs=db()->prepare("SELECT * FROM questions WHERE module_id=? ORDER BY id"); $qs->execute([$mid]); $questions=$qs->fetchAll(PDO::FETCH_ASSOC);
 $total=0;$score=0;
 foreach($questions as $q){$total+=(int)$q['marks']; if(isset($answers[$q['id']]) && trim((string)$answers[$q['id']])===$q['answer']) $score+=(int)$q['marks'];}
 $pct=$total?($score/$total*100):0; $status=$pct>=$module['pass_mark']?'passed':'failed';
 $s=db()->prepare("INSERT INTO attempts(user_id,module_id,started_at,submitted_at,score,total,status) VALUES(?,?,?,?,?,?,?)");
 $now=date('c');$s->execute([$uid,$mid,$now,$now,$pct,$total,$status]);$aid=(int)db()->lastInsertId();
 foreach($questions as $q){
   $ans=$answers[$q['id']]??'';$correct=trim((string)$ans)===$q['answer'];$aw=$correct?(int)$q['marks']:0;
   $x=db()->prepare("INSERT INTO responses(attempt_id,question_id,answer,correct,marks_awarded) VALUES(?,?,?,?,?)");
   $x->execute([$aid,$q['id'],$ans,$correct?1:0,$aw]);
 }
 header("Location: result.php?id=$aid");exit;
}
$qs=db()->prepare("SELECT * FROM questions WHERE module_id=? ORDER BY id");$qs->execute([$mid]);$questions=$qs->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><?php include 'partials/head.php'; ?></head><body><?php include 'partials/nav.php'; ?>
<main class="container narrow"><div class="card"><div class="exam-head"><div><div class="module-num">MODULE <?=$mid?></div><h1><?=e($module['title'])?></h1></div><div class="timer" id="timer">25:00</div></div>
<p class="muted">Answer all questions. This starter assessment is automatically marked. Pass mark: <?=$module['pass_mark']?>%.</p>
<form method="post" id="examForm">
<?php foreach($questions as $i=>$q): $opts=json_decode($q['options_json'],true); ?>
<div class="question"><div class="qtitle"><?=($i+1)?>. <?=e($q['question'])?> <span class="marks"><?=$q['marks']?> mark</span></div>
<?php foreach($opts as $o): ?><label class="option"><input type="radio" name="answer[<?=$q['id']?>]" value="<?=e($o)?>"> <?=e($o)?></label><?php endforeach; ?>
</div>
<?php endforeach; ?>
<button class="btn primary full" type="submit" onclick="return confirm('Submit this assessment?')">Submit assessment</button>
</form></div></main>
<script>
let sec=25*60; const el=document.getElementById('timer');
const t=setInterval(()=>{sec--;let m=Math.floor(sec/60),s=sec%60;el.textContent=String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');if(sec<=0){clearInterval(t);document.getElementById('examForm').submit();}},1000);
</script></body></html>