<p>Hi <?= $receiver_name ?>,</p>

<? 
if($athlete_id) {
	$url = "http://www.crossfitalerts.com?id=".$athlete_id;
}
?>

<? if($athlete_id): ?>
<p><a href="<?= $url ?>"><?= $name ?></a> has started following you on <a href="http://www.crossfitalerts.com">Crossfit Alerts</a>.</p>
<p><a href="<?= $url ?>">Click here</a> to start following <?= $name ?>.</p>
<? else: ?>
<p><?= $name ?> has started following you on <a href="http://www.crossfitalerts.com">Crossfit Alerts</a>.</p>
<? endif; ?>

<p>Sincerely,</p>

<p>Crossfit Alerts</p>
