<?php include('header.tpl.php') ?>
		<br>page-front.tpl.php :<br>
		
		<div style="background-color:#00ffff;"><?php print $header; ?></div><!-- pour la dev bar -->
		Landscape url : <?php echo $landscape;?><br>
		
		<?php include('menu.tpl.php') ?>
		
		<?php print $content ?>
		
		<div style="clear:both"></div>
		
		<div id="footer"><?php print $footer_message . $footer ?></div>
		
		<div style="clear:both"></div>
		<br>fin : page-front.tpl.php<br>
<?php include('footer.tpl.php') ?>
