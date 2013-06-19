<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
</script>
<div id="tabs">
	
<?php
	if (empty($discipline)) require_once('node-athlete_cat-tabs.tpl.php');
	$depth = count($breadcrumb);
	
	$list_athletes = array();
	$list_teams = array();
	foreach($breadcrumb[1]['below'] as $product) {
		$item = menu_get_item($product['link']['href']);
		$itemMap = $item['map'][1];
		if($itemMap->field_type_athlete[0]['value'] == 0)
			$list_athletes[] = $itemMap;
		else
			$list_teams[] = $itemMap;
	}
?>
	<div  class="athletes">
	
		<h2 class="helvetica" style="margin: 0 0 11px 5px;"><?php echo t('Teams'); ?></h2>
		<div class="list-athletes">
			<?php
			$i=0;
			foreach($list_teams as $athlete) {
				?>
				<a href="<?php echo url($athlete->path);?>"<?php print ($i%3 == 2)?' class="athletes-right"':'' ?>><img alt="" src="<?php echo base_path(). $athlete->field_athlete_photo[0]['filepath'] ?>" /><span class="helvetica"><?php echo $athlete->title; ?></span></a>
			<?php
				$i++;
				} // end for ?>
			<div class="clear">&nbsp;</div>
		</div>
		
		<h2 class="helvetica" style="margin: 0 0 11px 5px;"><?php echo t('Athletes'); ?></h2>
		<div class="list-athletes">
<?php 
			$i=0;
			foreach($list_athletes as $athlete) { 
				$name = $athlete->title;
				if(!empty($athlete->field_team_name[0]['value']))
					$name.= ' ('.$athlete->field_team_name[0]['value'].')';
?>
				<a href="<?php echo url($athlete->path);?>"<?php print ($i%3 == 2)?' class="athletes-right"':'' ?>><img alt="" src="<?php echo base_path(). $athlete->field_athlete_photo[0]['filepath'] ?>" /><span class="helvetica"><?php echo $name; ?></span></a>
<?php 
				$i++;
			} // end for 
?>
			<div class="clear">&nbsp;</div>
		</div>
		
	</div>
</div>
