<?php include('header.tpl.php') ?>
		<br>page-404.tpl.php :<br>
		
		<div style="background-color:#00ffff;"><?php print $header; ?></div><!-- pour la dev bar -->
		
		url home : <?php echo $front_page; ?><br>
		<div style="border: 1px solid #000000;float:left;">
			<?php print $content ?>
		</div>
		
		<div style="clear:both"></div>
		
		<div id="footer"><?php print $footer_message ?></div>
		
		<br>fin : page-404.tpl.php<br>
<?php include('footer.tpl.php') ?>