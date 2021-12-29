<? if($athlete['EmailQueue']['is_update']): ?>
	<p>
		<?= $athlete['Athlete']['name'] ?> updated <?= $athlete['Athlete']['gender'] == 'Male' ? 'his' : 'her' ?> score to <?= $athlete['EmailQueue']['score'] ?> on workout number 13.<?= $athlete['EmailQueue']['workout_number'] ?>. 
		<? if($athlete['EmailQueue']['rank']): ?>
		<?= $athlete['Athlete']['gender'] == 'Male' ? 'His' : 'Her' ?> current rank is <?= Util::addOrdinalNumberSuffix($athlete['EmailQueue']['rank']) ?> in this workout.
		<? endif; ?>
	</p>
<? else: ?>
	<p>
		<?= $athlete['Athlete']['name'] ?> scored <?= $athlete['EmailQueue']['score'] ?> on workout number 13.<?= $athlete['EmailQueue']['workout_number'] ?>. 
		<? if($athlete['EmailQueue']['rank']): ?>
		<?= $athlete['Athlete']['gender'] == 'Male' ? 'His' : 'Her' ?> current rank is <?= Util::addOrdinalNumberSuffix($athlete['EmailQueue']['rank']) ?> in this workout.
		<? endif; ?>
	</p>
<? endif; ?>

<p>
	That is equivalent to <?= Util::scoreToString($athlete['EmailQueue']['score'], $athlete['EmailQueue']['workout_number']); ?>.
</p>

<p>
	<a href="http://games.crossfit.com/athlete/<?= $athlete['Athlete']['gid']; ?>">Go here to see more details.</a>
</p>

<p>Cheers and if you have any questions, email Sean at <a href="mailto:crossfitalerts@gmail.com">crossfitalerts@gmail.com</a>.</p>

<p>Sean Falconer</p>