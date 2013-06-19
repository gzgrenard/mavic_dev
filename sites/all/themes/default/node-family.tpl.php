<br>node-family.tpl.php :<br>
	<?php if(count($breadcrumb[2]['below']) > 1) { ?>
		<div>
			TABS :
			<?php 
				foreach($breadcrumb[2]['below'] as $data) {
					if ($data['link']['in_active_trail']) $selected = "active";
					else $selected = "";
					echo l($data['link']['title'], $data['link']['href'])." $selected ";
				}
			?>
		</div>
	<?php } ?>

	<div style="border: 1px solid #00ffff;">
		Filters :<br>
<?php 
		foreach($field_filter_macro as $filter) {
			echo $filter['view'];
		}
?>
	</div>

	<div style="border: 1px solid #00ff00;">
<?php
		$ssc = false;
		$altium = false;
		foreach($breadcrumb[3]['below'] as $product) {
			$item = menu_get_item($product['link']['href']);
			$itemMap = $item['map'][1];
			$ssc = $ssc | $itemMap->field_ssc[0]['value'];
			$altium = $altium | $itemMap->field_altium[0]['value'];
			$model = node_load($itemMap->field_otherarticle[0]['nid']);
?>
			<a href="<?php echo url($product['link']['href']); ?>">
				img : <?php echo $theme_path.'/images/range/'.$model->title.'.jpg';?><br>
				nom : <?php echo check_plain($itemMap->title); ?><br>
				<?php if($itemMap->field_ssc[0]['value']) echo 'ssc logo : '.$theme_path.'/images/logos/ssc.gif<br>'; ?>
				<?php if($itemMap->field_altium[0]['value']) echo 'altium logo : '.$theme_path.'/images/logos/altium.gif<br>'; ?>
				texte info bulle : <?php echo check_plain($itemMap->field_usp[0]['value']); ?><br>
			</a>
<?php
			echo "filter tags id :";
			foreach($itemMap->field_filter_value as $tag) {
				echo $tag['nid']. ' ';
			}
?>
			<br><br>
<?php
		}
		
		if($ssc) {
			$sscBody = db_result(db_query('select r.body from {node_revisions} r INNER JOIN {node} n using (vid) where n.type="prodvalcarac" and n.`language`="'.$language.'" and r.title like "ssc"'));
			echo "ssc desc : ". $sscBody . "<br>";
		}
		
		if($altium) {
			$altiumBody = db_result(db_query('select r.body from {node_revisions} r INNER JOIN {node} n using (vid) where n.type="prodvalcarac" and n.`language`="'.$language.'" and r.title like "altium"'));
			echo "altium desc : ". $altiumBody . "<br>";
		}
?>
	</div>

<br>fin : node-family.tpl.php<br>