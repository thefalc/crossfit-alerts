<script type="text/javascript">
  $(document).ready(function() {
    $("#submit-btn").click(function(e) {
      e.preventDefault();

      $("#submit-btn").addClass("disabled");
      $('#form-contact').ajaxSubmit(processSubmission);
    });
  });

  function processSubmission(responseText) {
      $("#submit-btn").removeClass("disabled");

        var responseObject = eval('(' + responseText + ')');
        var message;
        if(responseObject.result == "FAILURE") {
            if (!responseObject.message) {
                message = "There was a problem saving sending your contact request.";
            } else {
                message = responseObject.message;
            }
            $("#error_dialog .error-message").html(message);
            $("#error_dialog").modal();
        }
        else {
            $(".input-block-level").val("");
            $("#success_dialog .success-message").html("Your contact request has been sent.");
            $("#success_dialog").modal();
        }
    }
</script>

<div class="container">
    <form class="form-contact" id="form-contact" action="/pages/contactSubmit" method="post">
        <h2 class="form-contact-heading">Contact Crossfit Alerts</h2>
        <? if(!isset($email)): ?>
          <input type="text" class="input-block-level" name="data[email]" placeholder="Your Email address">
        <? endif; ?>
        <textarea class="input-block-level" name="data[message]" placeholder="Feature suggestion, complaint, question? Let me know." style="height: 100px;"></textarea>
        <button style="float: right" id="submit-btn" class="btn large primary" type="submit">Send</button>
        <div class="cl">&nbsp;</div>
    </form>
</div>