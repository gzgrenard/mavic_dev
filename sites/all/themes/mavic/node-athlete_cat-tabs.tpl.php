<div id="tabs_buttons_gamme">
<?php foreach($menu_athlete as $data) : 
	$classes = 'tab';
	if ($data['link']['in_active_trail']) $classes.= " active";?>
	
	<div id="<?php print $data['link']['title']?>" 
	onmouseout="outTab(this)" 
	onmouseover="overTab(this)" 
	class="<?php echo $classes;?>">
	<a class="helvetica"  href="<?php echo url($data['link']['href']);?>" ><?php print($data['link']['title']); //$data['href']?></a>
	
	</div>
<?php endforeach; ?>
</div>
<div class="clear"></div>
