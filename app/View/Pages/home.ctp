<script type="text/javascript">
	var current = 0;
	var cancel = false;
	var running = false;

	$(document).ready(function() {
		$(window).scroll(function() {
		   if(!running && $(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
		   		if(!cancel) {
					running = true;
		   			// blockUI();
		        	loadMore();
				}
		   }
		});
	});

	function loadMore() {
		if(cancel) {
			running = false;
			// $.unblockUI();
			return;
		}

		current += 20;
		var query = $(".search-box").val();
		var remote_url = "/athletes/search/" + escape(query) + "?n=" + current;

    	$.ajax({
	        type: "POST",
	        url: remote_url,
	        success: function(html) {
	        	$("#athletes").append(html);

	        	if(html.length == 0) {
	        		cancel = true;
	        	}
	        	running = false;
	        }
	    });
	}
</script>

<? if($first_load): ?>
	<div class="alert-message success" style="width: 600px; margin: 0 auto;">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <strong>Welcome <?= $name; ?>!</strong> Start by choosing athletes to follow. We will email you with updates.<br/>Also,
	  make sure you <a href="/users/myAccount" title="Claim your account">claim your Crossfit games account.</a>
	</div>
<? elseif($claim && $this->Session->check("athlete_id") && !$this->Session->read("athlete_id")): ?>
	<div class="alert-message success" style="width: 600px; margin: 0 auto;">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <strong>Have a Crossfit Games account?</strong> Make sure you <a href="/users/myAccount" title="Claim your account">claim your Crossfit games account.</a>
	</div>
<? endif; ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div id="athletes">
			<?= $this->element("athletes", array("show_import" => true)); ?>
		</div>
		<div class="cl">&nbsp;</div>
	</div>
</div>
