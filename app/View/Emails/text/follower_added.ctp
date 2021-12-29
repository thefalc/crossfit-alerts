Hi <?= $receiver_name ?>,

<? 
if($athlete_id) {
	$url = "http://www.crossfitalerts.com?id=".$athlete_id;
}
?>

<?= $name ?> has started following you on CrossfitAlerts.com.

<? if($athlete_id): ?>
Use the linke below to start following <?= $name ?>:
<?= $url ?>
<? endif; ?>

Sincerely,

Crossfit Alerts