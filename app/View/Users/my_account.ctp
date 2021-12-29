<script type="text/javascript">
    $(document).ready(function() {
    	$("#claim-account-link").click(function(e) {
    		e.preventDefault();

    		$("#claim_account_dialog").modal();
    	});

    	$("#edit-account-link").click(function(e) {
    		e.preventDefault();

    		editAccount();
    	});

    	$("#save-account-btn").click(function(e) {
    		e.preventDefault();

    		$(this).addClass("disabled");
    		$('#accountForm').ajaxSubmit(processAccount);
    	});

    	$("#claim-account-btn").click(function(e) {
    		e.preventDefault();

    		$(this).addClass("disabled");
    		$('#claimAccountForm').ajaxSubmit(processSubmission);
    	});
    });

    function editAccount() {
    	$.getJSON("/users/getDetails", function(response) {
    		if(response.result == "SUCCESS") {
    			$("#account-name").val(response.name);
    			$("#account-email").val(response.email);

    			$("#account_dialog").modal();
    		}
    		else {
    			$("#error_dialog .error-message").html(response.message);
            	$("#error_dialog").modal();
    		}
    	});
    }

    function processAccount(responseText) {
    	$("#save-account-btn").removeClass("disabled");
		$("#account_dialog").modal('hide');

    	var responseObject = eval('(' + responseText + ')');
        var message;
        if(responseObject.result == "FAILURE") {
            if (!responseObject.message) {
                message = "There was a problem saving your information";
            } else {
                message = responseObject.message;
            }
            $("#error_dialog .error-message").html(message);
            $("#error_dialog").modal();
        }
        else {
        	$("#name-label").html($("#account-name").val());
        	$("#email-label").html($("#account-email").val());

            $("#success_dialog .success-message").html("You account information has been saved.");
            $("#success_dialog").modal();
        }
    }

    function processSubmission(responseText) {
    	$("#claim-account-btn").removeClass("disabled");
		$("#claim_account_dialog").modal('hide');

    	var responseObject = eval('(' + responseText + ')');
        var message;
        if(responseObject.result == "FAILURE") {
            if (!responseObject.message) {
                message = "There was a problem submitting your information";
            } else {
                message = responseObject.message;
            }
            $("#error_dialog .error-message").html(message);
            $("#error_dialog").modal();
        }
        else {
            $("#success_dialog .success-message").html("Thank you. We will contact you when we have linked your account.");
            $("#success_dialog").modal();
        }
    }
</script>

<div id="account_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Edit Account</h3>
		<div class="modal-body" style="margin: 0 auto; width: 280px;">
			<form id="accountForm" action="/users/save" method="post">
                <div>
                    <input type="text" id="account-name" class="input-block-level signup-form-input" style="width: 280px; font-size: 20px;" placeholder="Enter your name" name="data[User][name]" />
                </div>
                <div style="margin-top: 20px;">
                    <input type="text" id="account-email" class="input-block-level signup-form-input" style="width: 280px; font-size: 20px;" placeholder="Enter your email" name="data[User][email]" />
                </div>
            </form>
		</div>
		<div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		    <a href="#" id="save-account-btn" class="btn success">Save</a>
	   	</div>
	</div>
</div>

<div id="claim_account_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Claim Crossfit Games Account</h3>
		<div class="modal-body" style="margin: 0 auto; width: 300px;">
			<form id="claimAccountForm" action="/users/claimAccount/" method="post">
                <div>
                    <textarea class="input-block-level" style="width: 300px; font-size: 16px; line-height: 24px; height: 100px;" placeholder="Tell me who you are so I can connect your Crossfit Alerts account with your Games profile." name="data[message]"></textarea>
                </div>
            </form>
		</div>
		<div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		    <a href="#" id="claim-account-btn" class="btn success">Submit</a>
	   	</div>
	</div>
</div>

<div class="ui-box-shadow" style="width: 600px; margin: 0 auto; padding: 10px;">
	<h2 style="float: left;">Account Information</h2><a style="float: left; margin-top: 10px; margin-left: 5px;" href="#" id="edit-account-link" title="Edit Account">(edit)</a>
	<div class="cl">&nbsp;</div>

	<div class="modal-footer">
		<p class="athlete-value" id="name-label"><?= $user['User']['name'] ?></p>
		<p class="athlete-value" id="email-label"><?= $user['User']['email'] ?></p>
	</div>

	<h2>Activity</h2>
	<div class="modal-footer">
		<p><span class="athlete-label">Following:</span> <a href="/users/follows" style="font-weight: bold;" title="My Follows"><?= $user['0']['follows']; ?> <?= ($user['0']['follows'] == 1 ? ' person' : ' people') ?></a></p>
		<? if($user['0']['following_me']): ?>
		<p><span class="athlete-label">Followers:</span> <a href="/users/followers" style="font-weight: bold;" title="My Followers"><?= $user['0']['following_me'] ?> <?= ($user['0']['following_me'] == 1 ? ' person' : ' people') ?></a></p>
		<? else: ?>
			<p><span class="athlete-label">Followers:</span> <?= $user['0']['following_me'] ?> <?= ($user['0']['following_me'] == 1 ? ' person' : ' people') ?></p>
		<? endif; ?>
	</div>

	<h2>Athlete Information</h2>
	<div class="modal-footer">
		<? if($user['Athlete']['id']): ?>
			<div>
				<div style="float: left;">
					<img src="<?= $user['Athlete']['image'] ?>" width="184" height="184" />
				</div>
				<div style="float: left; margin-left: 20px;">
					<div class="athlete-detail-row"><span class="athlete-label">Region: </span><span class="athlete-value"><?= $user['Athlete']['region'] ?></span></div>
					<div class="athlete-detail-row"><span class="athlete-label">Affiliate: </span><span class="athlete-value"><?= $user['Athlete']['affiliate'] ?></span></div>
					<div class="athlete-detail-row"><span class="athlete-label">Age: </span><span class="athlete-value"><?= $user['Athlete']['age'] ?></span></div>
					<div class="athlete-detail-row"><span class="athlete-label">Height: </span><span class="athlete-value"><?= $user['Athlete']['height'] ?></span></div>
					<div class="athlete-detail-row"><span class="athlete-label">Weight: </span><span class="athlete-value"><?= $user['Athlete']['weight'] ?></span></div>
					<div class="athlete-detail-row"><span class="athlete-value"><a href="http://games.crossfit.com/athlete/<?= $user['Athlete']['gid'] ?>" target="_blank">Visit Games Profile</a></span></div>
				</div>
			</div>
		<? else: ?>
			<a href="#" id="claim-account-link">Claim your Crossfit Games profile</a>
		<? endif; ?>
	</div>
</div>