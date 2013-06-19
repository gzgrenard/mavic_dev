<div id="menu" style="border: 1px solid #ff0000;width:400px;float:left;">
	MENU :<br>
	<?php
		
		foreach ($primary_links as $data) {
			echo $data['link']['title'];
			if ($data['link']['in_active_trail']) echo ' actif';
			echo "<br>\n";
			foreach ($data['below'] as $subdata) {
				$subsubElem = @reset($subdata['below']);
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.l($subdata['link']['title'], $subsubElem['link']['href'], $subdata['link']['localized_options']);
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$theme_path.'/images/menus/'.$subdata['link']['options']['attributes']['title'].".jpg<br>\n";
			}
		}
	switch($active_menu_name) {
		case 'secondary-links' :
		case 'menu-technologies' :
		case 'menu-news' :
			$active = ' actif';
		break;
		default :
			$active = '';
	}
	?>
	MAVIC <?php echo $active; ?> :<br>
	<?php if($menu = @reset($menu_technologies)) { ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo url($menu['link']['href'])?>"><?php echo t('Technologie') ?></a><br>
	<?php } ?>
	<?php
		foreach ($secondary_links as $data) {
			echo l($data['link']['title'], $data['link']['href'], $data['link']['localized_options']);
			if ($data['link']['in_active_trail']) echo ' actif';
			echo "<br>\n";
		}
	?>
</div>


