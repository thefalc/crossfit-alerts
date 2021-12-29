<? $query = isset($query) ? $query : ""; ?>

<script type="text/javascript">
    function blockUI() {
        $.blockUI({ css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff' 
        } }); 
    }

    $(document).ajaxStop(function(r,s) {
        $.unblockUI();
    });

    $(document).ready(function() {
    	$("#signup").click(function(e) {
    		e.preventDefault();

    		$("#signup_dialog").modal();

            $("#signup_dialog").on('shown', function() {
                $("#name").focus();
            });
    	});

    	$("#login").click(function(e) {
    		e.preventDefault();

    		$("#login_dialog").modal();

            $("#login_dialog").on('shown', function() {
                $("#login-email").focus();
            });
    	});

    	$("#login-link").click(function(e) {
    		e.preventDefault();

    		$("#signup_dialog").modal('hide');
    		$("#login_dialog").modal();

            $("#login_dialog").on('shown', function() {
                $("#login-email").focus();
            });
    	});

        $(".login-form-input").keypress(function(e) {
            if(e.keyCode == 13) {
                busyLink("#login-btn");
                $(this).addClass("disabled");

                $('#loginForm').ajaxSubmit(processSubmission);
            }
        });

        $(".signup-form-input").keypress(function(e) {
            if(e.keyCode == 13) {
                busyLink("#login-btn");
                $(this).addClass("disabled");

                $('#joinForm').ajaxSubmit(processSubmission);
            }
        });

        $(".search-box").keypress(function(e) {
            if(e.keyCode == 13) {
                search();
            }
        });

    	$("#sign-up-btn").click(function(e) {
    		e.preventDefault();

            busyLink("#sign-up-btn");
            $(this).addClass("disabled");

    		$('#joinForm').ajaxSubmit(processSubmission);
    	});

    	$("#login-btn").click(function(e) {
    		e.preventDefault();

            busyLink("#login-btn");
            $(this).addClass("disabled");

    		$('#loginForm').ajaxSubmit(processSubmission);
    	});

    	$("#search-btn").click(function(e) {
    		e.preventDefault();

    		search();
    	});
    });

    function search() {
        blockUI();

        current = 0;
        cancel = false;

    	var query = escape($(".search-box").val());

        window.location.href = "/?q="+query;
    }

    var dialog = "";

    function processSubmission(responseText) {
        unbusyLink();
        $("#sign-up-btn").removeClass("disabled");
        $("#login-btn").removeClass("disabled");

        if($("#signup_dialog").css("display") != undefined && $("#signup_dialog").css("display") != "none") {
            dialog = "#signup_dialog";
        } 
        else {
            dialog = "#login_dialog";
        }

		$("#signup_dialog").modal('hide');
		$("#login_dialog").modal('hide');

    	var responseObject = eval('(' + responseText + ')');
        var message;
        if(responseObject.result == "FAILURE") {
            if (!responseObject.message) {
                message = "There was a problem saving your information";
            } else {
                message = responseObject.message;
            }
            $("#error_dialog .error-message").html(message);
            $("#error_dialog").on('hidden', function() {
                $(dialog).modal('show');
            });
            $("#error_dialog").modal();
        }
        else {
            window.location.reload();
        }
    }

    function busyLink(linkId, message) {
        if ($("#deleteMe").length == 0) {
            $(linkId).after('<div style="float: left; margin-left: 5px; margin-top: 5px;" id="deleteMe"><img src="/img/loading_small.gif" /></div>');
        }
    }

    function unbusyLink() {
        $("#deleteMe").remove();
    }
</script>

<div id="success_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Success!</h3>
        <div class="modal-body">
            <p class="success-message"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>
</div>

<div id="error_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Error</h3>
		<div class="modal-body">
			<p class="error-message"></p>
		</div>
		<div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	   	</div>
	</div>
</div>

<div id="signup_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Sign Up and Start Receiving Alerts</h3>
		<div class="modal-body" style="margin: 0 auto; width: 280px;">
			<form id="joinForm" action="/users/saveJoinInfo/" method="post">
                <div>
                    <input type="text" id="name" class="input-block-level signup-form-input" style="width: 280px; font-size: 20px;" placeholder="Enter your name" name="data[User][name]" />
                </div>
                <div style="margin-top: 20px;">
                    <input type="text" id="email" class="input-block-level signup-form-input" style="width: 280px; font-size: 20px;" placeholder="Enter your email" name="data[User][email]" />
                </div>
                <div style="margin-top: 20px;">
                    <input type="password" id="password" class="input-block-level signup-form-input" placeholder="Choose password" style="width: 280px; font-size: 20px;" name="data[User][password]" />
                </div>
            </form>

            <div style="margin-top: 5px;">
            	Already have an account? <a href="#" id="login-link">Login Now</a>
            </div>
		</div>
		<div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		    <a href="#" id="sign-up-btn" class="btn success">Sign Up</a>
	   	</div>
	</div>
</div>

<div id="login_dialog" class="modal hide fade" style="width: 400px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Login</h3>
		<div class="modal-body" style="margin: 0 auto; width: 280px;">
			<form id="loginForm" action="/users/login/" method="post">
                <div>
                    <input type="text" id="login-email" class="input-block-level login-form-input" style="width: 280px; font-size: 20px;" placeholder="Enter your email" name="data[User][email]" />
                </div>
                <div style="margin-top: 20px;">
                    <input type="password" class="input-block-level login-form-input" placeholder="Enter your password" style="width: 280px; font-size: 20px;" name="data[User][password]" />
                </div>
            </form>
		</div>
		<div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		    <a href="#" id="login-btn" class="btn success">Login</a>
	   	</div>
	</div>
</div>

<div class="topbar" data-dropdown="dropdown">
	<div class="topbar-inner">
		<div class="container">
			<div style="padding: 8px 20px 8px; margin-top: 5px; margin-left: -20px; display: block; float: left; line-height: 1;"><a style="color: #fff; font-size: 20px;" href="/">Crossfit Alerts</a></div>

			<div style="float: left; margin: 8px 20px 0;">
				<input type="text" class="search-box" value="<?= $query ?>" size="35" placeholder="Search by name, affiliate or region" />
				<a class="btn" id="search-btn" style="height: 14px;">
					<img src="/img/search.gif" alt="">
				</a>
				<div class="cl">&nbsp;</div>
			</div>

			<? if(!$loggedIn): ?>
                <div style="float: right; margin-top: 5px; margin-left: 10px;">
                    <span style="display: inline-block;"><a href="#" id="signup" class="btn success" title="Sign Up">Sign Up</a></span>
                    <span style="display: inline-block; margin-left: 5px;"><a style="color: #333" href="#" id="login" class="btn" title="Login">Login</a></span>
                </div>

				<ul class="nav secondary-nav">
                    <li><a href="http://seanfalconer.blogspot.com/" target="_blank" id="login" title="Blog">Blog</a></li>
                    <li class="<?= ($this->params->url == 'pages/contact' ? 'active' : ''); ?>"><a href="/pages/contact" title="Contact">Contact</a></li>
				</ul>                
			<? else: ?>
				<ul class="nav secondary-nav">
					<li class="<?= ($this->params->url == '' ||  $this->params->url == 'pages/home' ? 'active' : ''); ?>"><a href="/" title="Home">Home</a></li>
					<li class="<?= ($this->params->url == 'users/follows' ? 'active' : ''); ?>"><a href="/users/follows" title="My Follows">My Follows</a></li>
                    <li class="<?= ($this->params->url == 'users/followers' ? 'active' : ''); ?>"><a href="/users/followers" title="My Followers">Followers</a></li>
                    <li><a href="http://seanfalconer.blogspot.com/" target="_blank" title="Blog">Blog</a></li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><?= $name ?></a>
                        <ul class="dropdown-menu">
                            <li class="<?= ($this->params->url == 'users/myAccount' ? 'active' : '') ?>"><a href="/users/myAccount" title="My Profile">My Account</a></li>
                            <li class="<?= ($this->params->url == 'pages/tools' ? 'active' : ''); ?>"><a href="/pages/tools" title="Tools">Tools</a></li>
                            <li class="<?= ($this->params->url == 'pages/contact' ? 'active' : ''); ?>"><a href="/pages/contact" title="Contact">Contact</a></li>
                            <li class="divider">&nbsp;</li>
                            <li><a title="Log Out" href="/users/logout">Log Out</a></li>
                        </ul>
                    </li>
				</ul>
			<? endif; ?>
		</div>
	</div>
</div>