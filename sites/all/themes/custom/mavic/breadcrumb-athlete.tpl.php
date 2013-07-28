<div style="float:left">
	<b><?php echo l($breadcrumb[0]['link']['title'],$breadcrumb[0]['link']['href']); ?></b>
	<?php echo ' > '. l(t('athletes') .' - '. $breadcrumb[1]['link']['title'],$breadcrumb[1]['link']['href']);?>
	>&nbsp;&nbsp;
</div>

<?php
	$list_athletes = array();
	$list_teams = array();
	foreach($breadcrumb[1]['below'] as $product) {
		$item = menu_get_item($product['link']['href']);
		$itemMap = $item['map'][1];
		if($product['link']['in_active_trail']) {
			if($itemMap->field_type_athlete[0]['value'] == 0)
				$type = ''; //t('athletes');
			else
				$type = t('team').' - ';
		} else {
			if($itemMap->field_type_athlete[0]['value'] == 0) {
				$list_athletes[] = $product;
			} else {
				$list_teams[] = $product;
			}
		}
	}
	
?>
<div id="select_page">
	<div id="selected_product" onclick="show_hide_select('#list_select')"><?php echo $type.$breadcrumb[2]['link']['title']; ?></div>
	<ul id="list_select">
		<?php 
			foreach($list_teams as $product) {
				if(!$product['link']['in_active_trail'])
					echo '<li><a href="'.url($product['link']['href']).'">'.t('team').' - '.$product['link']['title']."</a></li>";
			}
			
			foreach($list_athletes as $product) {
				if(!$product['link']['in_active_trail'])
					echo '<li><a href="'.url($product['link']['href']).'">'.$product['link']['title']."</a></li>";
			}
		?>
	</ul>
	<script type="text/javascript">
		$('#body').click( function(){ $('#list_select').css('display','none'); });
	</script>
</div>
