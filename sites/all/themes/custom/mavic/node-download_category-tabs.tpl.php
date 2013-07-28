<div id="tabs_buttons_gamme">
<?php
foreach($menu_download as $data) : 
	$classes = 'tab';
	if ($data['link']['in_active_trail']) $classes.= " active";?>
	
	<div id="<?php print $data['link']['title']?>" 
	onmouseout="outTab(this)" 
	onmouseover="overTab(this)" 
	class="<?php echo $classes;?>">
	<?php
		if($data['link']['expanded']) {
			$first = reset($data['below']);
			$link = $first['link']['href'];
		} else {
			$link = $data['link']['href'];
		}
	?>
	<a class="helvetica"  href="<?php echo url($link);?>" >
		<?php print($data['link']['title']); ?>
	</a>
	
	</div>
	
<?php endforeach; ?>
</div>
<div class="clear"></div>
