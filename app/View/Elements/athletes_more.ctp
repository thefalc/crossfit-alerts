<? foreach($athletes as $athlete): ?>
	<? 
		$overlay_background = "rgba(0, 100, 0, .5)";
		$background = "#fff";
		if($athlete['0']['follow']) {
			$background = "#CDFECD"; 
			$overlay_background = "rgba(255, 0, 0, 0.6)";
		}
	?>

	<div athlete_id="<?= $athlete['Athlete']['id'] ?>" following="<?= $athlete['0']['follow'] ?>" class="ui-box-shadow athlete" style="background: <?= $background ?>;margin-top: 20px; float: left; width: 184px; height: 450px; padding: 20px; margin-left: 20px; position: relative;">
		<div class="follow-btn" style="position: absolute; top: 0px; left: 0px; display: none; padding: 10px; background: rgb(54, 54, 54); background: <?= $overlay_background ?>; font-weight: bold; color: #fff">
			<? if($athlete['0']['follow']): ?>
				Stop receiving alerts
			<? else: ?>
				Receive alerts about this athlete
			<? endif; ?>
		</div>
		<h2 style="text-align: center; height: 40px; font-size: 20px;"><?= Util::truncate($athlete['Athlete']['name'], 18, true); ?></h2>
		<div style="text-align: center;"><img width="184" height="184" title="<?= $athlete['Athlete']['name']; ?>" src="<?= $athlete['Athlete']['image'] ?>" /></div>

		<div style="margin-top: 20px;">
			<div class="athlete-detail-row">
				<div class="athlete-label">Region</div>
				<div class="athlete-value"><?= $athlete['Athlete']['region'] ?></div>
			</div>

			<div class="athlete-detail-row">
				<div class="athlete-label">Affiliate</div>
				<div class="athlete-value"><?= $athlete['Athlete']['affiliate'] ?></div>
			</div>

			<div class="athlete-detail-row" style="border-top: 1px solid #e7e7e7; padding-top: 10px; margin-top: 10px;">
				<span class="athlete-label">Age:</span>
				<span class="athlete-value"><?= $athlete['Athlete']['age'] ?></span>
				<span class="athlete-label" style="display: inline-block; margin-left: 2px;">Ht:</span>
				<span class="athlete-value"><?= $athlete['Athlete']['height'] ?></span>
				<span class="athlete-label" style="display: inline-block; margin-left: 2px;">Wt:</span>
				<span class="athlete-value"><?= $athlete['Athlete']['weight'] ?></span>
			</div>
		</div>

		<div style="margin-top: 10px; border-top: 1px solid #e7e7e7; padding-top: 10px;">
			<div class="athlete-detail-row">
				<span class="athlete-label">13.1: </span>
				<span class="athlete-value wod-value" data-original-title="Workout 13.1" data-toggle="popover" data-placement="top" data-content="<?= Util::scoreToString($athlete['AthleteScore']['wod1'], 1); ?>" style="display: inline-block; width: 30px;"><?= $athlete['AthleteScore']['wod1'] ?></span>
				<span class="athlete-label" style="display: inline-block; margin-left: 10px;">13.2: </span>
				<span class="athlete-value wod-value"  data-original-title="Workout 13.2" data-toggle="popover" data-placement="top" data-content="<?= Util::scoreToString($athlete['AthleteScore']['wod2'], 2); ?>" style="display: inline-block; width: 30px;"><?= $athlete['AthleteScore']['wod2'] ?></span>
			</div>
			<div class="athlete-detail-row">
				<span class="athlete-label">13.3: </span>
				<span class="athlete-value wod-value"  data-original-title="Workout 13.3" data-toggle="popover" data-placement="top" data-content="<?= Util::scoreToString($athlete['AthleteScore']['wod3'], 3); ?>" style="display: inline-block; width: 30px;"><?= $athlete['AthleteScore']['wod3'] ?></span>
				<span class="athlete-label" style="display: inline-block; margin-left: 10px;">13.4: </span>
				<span class="athlete-value wod-value"  data-original-title="Workout 13.4" data-toggle="popover" data-placement="top" data-content="<?= Util::scoreToString($athlete['AthleteScore']['wod4'], 4); ?>" style="display: inline-block; width: 30px;"><?= $athlete['AthleteScore']['wod4'] ?></span>
			</div>
			<div class="athlete-detail-row">
				<span class="athlete-label">13.5: </span>
				<span class="athlete-value wod-value"  data-original-title="Workout 13.5" data-toggle="popover" data-placement="top" data-content="<?= Util::scoreToString($athlete['AthleteScore']['wod5'], 5); ?>" style="display: inline-block; width: 30px;"><?= $athlete['AthleteScore']['wod5'] ?></span>
			</div>
		</div>
	</div>
<? endforeach; ?>
