<div id="tabs_buttons_gamme">
<?php
if($menu_technologies) :
foreach($menu_technologies as $data) : 
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
	<a class="helvetica"  href="<?php echo url($link);?>" ><?php print(mb_strtoupper($data['link']['title'])); //$data['href']?></a>
	
	</div>
	
<?php endforeach;
endif; ?>
</div>
<div class="clear"></div>
