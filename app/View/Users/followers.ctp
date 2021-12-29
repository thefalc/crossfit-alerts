<script type="text/javascript">
    $(document).ready(function() {
    	$(document).on("click", ".athlete", function() {
			toggleFollow(this);
        });
	});

	function toggleFollow(object) {
		$.getJSON("/athletes/toggleFollow/" + $(object).attr("athlete_id"), function(response) {
			if(response.result == "SUCCESS") {
				if(response.follow == true) {
                    $(object).removeClass("success");
                    $(object).html("Un-Follow");
				}
				else {
                    $(object).addClass("success");
                    $(object).html("Follow");
				}
			}
			else {
				$("#error_dialog .error-message").html(response.message);
            	$("#error_dialog").modal();
			}
		});
	}
</script>

<? if($users && count($users) > 0): ?>
	<h1 style="text-align: center; margin-top: 10px;">People Following You</h1>
	<div class="ui-box-shadow" style="width: 400px; margin: 20px auto; padding: 10px;">
		<? $first = true; foreach($users as $user): ?>
			<div style="margin-bottom: 10px; <?= $first ? 'border: none;' : '' ?>" class="modal-footer">
				<div style="float: left; width: 200px; font-size: 16px; margin-top: 3px;">
					<? if($user['User']['athlete_id']): ?>
						<a style="font-size: 16px;" href="/?id=<?= $user['User']['athlete_id']; ?>" title="<?= $user['User']['name']; ?>"><?= $user['User']['name']; ?></a>
					<? else: ?>
						<?= $user['User']['name']; ?>
					<? endif; ?>
				</div>
				<div style="float: left; margin-left: 10px;">
					<? if($user['User']['athlete_id']): ?>
						<? if($user['0']['is_following']): ?>
							<a href="#" style="width: 60px; text-align: center;" athlete_id="<?= $user['User']['athlete_id'] ?>" class="btn athlete">Un-Follow</a>
						<? else: ?>
							<a href="#" style="width: 60px; text-align: center;" athlete_id="<?= $user['User']['athlete_id'] ?>" class="athlete btn success">Follow</a>
						<? endif; ?>
					<? else: ?>
						<p>No athlete profile available</p>
					<? endif; ?>
				</div>
				<div class="cl">&nbsp;</div>
			</div>
		<? $first = false; endforeach; ?>
	</div>
<? else: ?>
	<h1 style="text-align: center; margin-top: 10px;">No one is following you :-(</h1>

	<? if(!$athlete_id): ?>
		<div class="alert-message warning" style="text-align: center; margin: 20px auto; width: 400px;">
			You need to <a href="/users/myAccount" title="Claim your account">claim your Crossfit games account</a> for this to work.
		</div>
	<? endif; ?>
<? endif; ?>