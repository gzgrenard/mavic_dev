<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	
	<head>
	
		<title><?php print $head_title ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<?php print $styles ?>
		<?php print $scripts ?>
	</head>
	
	<body id="body">
		<br>maintenance-page.tpl.php :<br>
		<div style="background-color:#00ffff;"><?php print $header; ?></div><!-- pour la dev bar -->
		
		<?php print $content ?>
		
		<div style="clear:both"></div>
		<br>fin : maintenance-page.tpl.php<br>
	</body>
</html>