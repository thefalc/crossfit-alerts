<script type="text/javascript">
    $(document).ready(function() {
    	$(document).on("mouseenter", ".athlete", function() {
    		$(this).css("background", "#FFFBCC");
            $(this).css("cursor", "pointer");
            $(this).find(".follow-btn").css("display", "block");
        });

        $(document).on("mouseenter", ".wod-value", function() {
            $(this).popover('show');
        });

        $(document).on("mouseleave", ".wod-value", function() {
            $(this).popover('hide');
        });
    	
    	$(document).on("mouseleave", ".athlete", function() {
    		if($(this).attr("following") == 1) {
    			$(this).css("background", "#CDFECD");
    		}
    		else {
    			$(this).css("background", "#ffffff");
    		}

            $(this).css("cursor", "default");
            $(this).find(".follow-btn").css("display", "none");
        });
    	
    	$(document).on("click", ".athlete", function() {
    		<? if($loggedIn): ?>
    			toggleFollow(this);
    		<? else: ?>
            	$("#signup_dialog").modal();
            <? endif; ?>
        });

        $("#submit-btn").click(function(e) {
            e.preventDefault();

            busyLink("#submit-btn");
            $(this).addClass("disabled");

            importProfile();
        });
	});

    function importProfile() {
        var url = $("#games_url").val();

        $.getJSON("/crawlers/importProfile?u=" + url, function(response) {
            $("#submit-btn").removeClass("disabled");
            unbusyLink();

            if(response.result == "FAILURE") {
                $("#error_dialog .error-message").html(response.message);
                $("#error_dialog").modal();
            }
            else {
                window.location.href = "/?id=" + response.id;
            }
        });
    }

	function toggleFollow(object) {
		$.getJSON("/athletes/toggleFollow/" + $(object).attr("athlete_id"), function(response) {
			if(response.result == "SUCCESS") {
				if(response.follow == true) {
                    $(object).find(".follow-btn").css("background", "rgba(255, 0, 0, 0.6)");
					$(object).find(".follow-btn").html("Stop receiving alerts");
					$(object).attr("following", "1");
					$(object).css("background", "#CDFECD");
				}
				else {
                    $(object).find(".follow-btn").css("background", "rgba(0, 100, 0, .5)");
					$(object).find(".follow-btn").html("Receive alerts about this athlete");
					$(object).attr("following", "0");
					$(object).css("background", "#fff");
				}
			}
			else {
				$("#error_dialog .error-message").html(response.message);
            	$("#error_dialog").modal();
			}
		});
	}
</script>
<?
    $message = isset($message) ? $message : "Sorry, unable to locate a matching athlete.";
    $show_import = isset($show_import) ? $show_import : false;
?>

<? if(!$athletes || !count($athletes)): ?>
	<h2 style="font-size: 18px;"><?= $message ?></h2>

    <div style="margin-top: 10px;">
        <a href="/" title="">Show me all athletes.</a>
    </div>

    <? if($show_import): ?>
        <div style="margin-top: 20px; width: 580px; padding: 10px;" class="ui-box-shadow">
            <h2 style="font-size: 18px">Is there an athlete missing?</h2>
            <div>
                <p>Help me out and add this athlete to our system.</p>
                <p>Paste the URL to their athlete profile on <a href="http://games.crossfit.com" target="_blank">games.crossfit.com</a> below and click the submit button.</p>

                <form style="margin-top: 20px; margin-bottom: 20px;">
                    <input type="text" id="games_url" class="input-block-level" style="float: left; width: 400px; font-size: 20px;" placeholder="Enter games profile URL" name="data[url]" />
                    <a href="#" id="submit-btn" style="float: left; margin-left: 10px;" class="btn">Submit</a>
                    <div class="cl">&nbsp;</div>
                </form>
                <p style="color: #999">Should look similar to http://games.crossfit.com/athletes/XXXXX (where the XXXXX is a number).</p>
            </div>
        </div>
    <? endif; ?>
<? endif; ?>

<?= $this->element("athletes_more"); ?>

