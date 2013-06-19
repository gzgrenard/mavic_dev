<?php include('header.tpl.php') ?>
		<br>page.tpl.php :<br>
		
		<div style="background-color:#00ffff;"><?php print $header; ?></div><!-- pour la dev bar -->
		Landscape url : <?php echo $landscape;?><br>
		
		<?php include('menu.tpl.php') ?>
		
		<div style="border: 1px solid #000000;float:left;">
			<?php print $content ?>
		</div>
		
		<div style="clear:both"></div>
		
		<div id="footer"><?php print $footer_message . $footer ?></div>
		
		<br>fin : page.tpl.php<br>
<?php include('footer.tpl.php') ?>