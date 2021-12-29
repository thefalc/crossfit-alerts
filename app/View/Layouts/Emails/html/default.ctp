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
 * @package       app.View.Layouts.Email.html
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <title>Crossfit Alerts - Follow your favorite athletes</title>
    <style type=3D"text/css">
	h1 {
		color: #e64372 !important;
	}
	a.btn {
		color: #ffffff !important;
	}
	 @media only screen and (min-device-width : 320px) and (max-device-width =
	:
	1024px) {
	 .trackingPixels {
		width: 650px !important;
	}
	html {
		-webkit-text-size-adjust: none !important;
	}
	body {
		padding-left: 40px !important;
	}
	}
	</style>
    <meta name="viewport" content="width=device-width, maximum-scal=e=1,minimum-scale=1, user-scalable=no"/>
</head>
<body style="background:#F7F7F7;-webkit-text-size-adjust:none;">
	<table align="center" cellpadding="0" cellspacing="0" style="width:570px;">
		<tbody>
			<tr>
				<td class="pageContainer" align="center" style="color: #333333; width: 570px; font-family: Arial, Helvetica, sans-serif;">
					<table align="center" cellpadding="0" cellspacing="0" style="width:570px;">
						<tbody>
							<tr>
								<td style="padding: 15px 0 10px; font-size: 20px; color: #404040" align="center">Crossfit Alerts</td>
							</tr>
							<tr>
								<td style="border-bottom: 1px solid #dcdcdc; color: #e64372; padding: 0 015px; font-family: Arial, Helvetica, sans-serif;" align="center">
									<h1 style="font-size: 16px; margin: 0; color: #e64372 !important; font-weight: bold; text-shadow: 0 0 1px #ffffff;">The Easy Way To Follow Your Favorite Athletes</h1>
								</td>
							</tr>
							<tr>
								<td style="border-top: 1px solid #ffffff; padding: 10px 20px; width: 100%; font-family: Arial, Helvetica, sans-serif;" align="left">
									<?php echo $this->fetch('content'); ?>
								</td>
							</tr>
							<tr>
								<td style="border-bottom: 1px solid #dcdcdc; padding: 10px 0 0 0;font-family: Arial, Helvetica, sans-serif; font-size: 16px;" align="center">&nbsp;</td>
							</tr>
							<tr>
								<td class="footer" style="border-top: 1px solid #ffffff; padding: 10px 0 30px; font-family: Arial, Helvetica, sans-serif;" align="center">
									<p style="font-size: 11px; line-height: 16px; color: #999999;">You received this email because you signed up Crossfit Alerts.</p>
									<p style="font-size: 11px; line-height: 16px; color: #999999;">Please contact <a href="mailto:crossfitalerts@gmail.com">crossfitalerts@gmail.com</a> if you need any help.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>