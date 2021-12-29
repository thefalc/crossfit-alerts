<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>

	<script language="javascript" type="text/javascript" src="/js/jquery-1.6.1.min.js?<?= filemtime(__DIR__.'/../../webroot/js/jquery-1.6.1.min.js'); ?>"></script>
    <script language="javascript" type="text/javascript" src="/js/jquery-ui-1.8.2.custom.min.js?<?= filemtime(__DIR__.'/../../webroot/js/jquery-ui-1.8.2.custom.min.js'); ?>"></script>
	<script language="javascript" type="text/javascript" src="/js/bootstrap.min.js?<?= filemtime(__DIR__.'/../../webroot/js/bootstrap.min.js'); ?>"></script>
	<script language="javascript" type="text/javascript" src="/js/jquery.form.js?<?= filemtime(__DIR__.'/../../webroot/js/jquery.form.js'); ?>"></script>
	<script language="javascript" type="text/javascript" src="/js/jquery.blockUI.js?<?= filemtime(__DIR__.'/../../webroot/js/jquery.blockUI.js'); ?>"></script>
	<script language="javascript" type="text/javascript" src="/js/bootstrap-dropdown.js?<?= filemtime(__DIR__.'/../../webroot/js/bootstrap-dropdown.js'); ?>"></script>

	<link rel="stylesheet" href="/css/bootstrap.css?<?= filemtime(__DIR__.'/../../webroot/css/bootstrap.css'); ?>" type="text/css" />
	<link rel="stylesheet" href="/css/bootstrap-responsive.css?<?= filemtime(__DIR__.'/../../webroot/css/bootstrap-responsive.css'); ?>" type="text/css" />
	<link rel="stylesheet" href="/css/custom-theme/jquery-ui-1.8.16.custom.css?<?= filemtime(__DIR__.'/../../webroot/css/custom-theme/jquery-ui-1.8.16.custom.css'); ?>" type="text/css" />
	<link rel="stylesheet" href="/css/styles.css?<?= filemtime(__DIR__.'/../../webroot/css/styles.css'); ?>" type="text/css" />

	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-39332547-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>

	<?php
		echo $this->Html->meta('icon');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body style="background: white url('/img/bg-w.gif') repeat">
	<div id="container">
		<?php echo $this->element("layout/top_panel"); ?>

		<div id="content" style="padding-top: 60px;">
			<?
				$flash = $this->Session->flash();
				if($flash) {
					echo "<div class='alert-message warning' style='width: 600px; margin: 0 auto;'><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>".$flash."</div>";
				}
			?>
			

			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
