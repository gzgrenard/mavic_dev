<br>node-macromodel.tpl.php :<br>

	<div style="border: 1px solid #00ff00;">
		default view : <?php echo $product_path.'/normal/'.$list_color[$default_color]->url_default; ?><br>
		zoom view : <?php echo $product_path.'/zoom/'.$list_color[$default_color]->url_default; ?><br>
		<?php if (!empty($list_color[$default_color]->url_front)) { ?>
			front bouton view :<?php echo $product_path.'/normal/'.$list_color[$default_color]->url_front; ?><br><br>
		<?php } ?>
		<?php if (!empty($list_color[$default_color]->url_top)) { ?>
			top bouton view :<?php echo $product_path.'/normal/'.$list_color[$default_color]->url_top; ?><br><br>
		<?php } ?>
		list color js :<br>
		<?php foreach($list_color as $key => $color) { ?>
			bouton color view : <?php echo $product_path.'/color/'.$color->url_default; ?><br>
			default view : <?php echo $product_path.'/normal/'.$color->url_default; ?><br>
			zoom view : <?php echo $product_path.'/zoom/'.$color->url_default; ?><br>
			<?php if (!empty($color->url_front)) { ?>
				front bouton view :<?php echo $product_path.'/normal/'.$color->url_front; ?><br><br>
			<?php } ?>
			<?php if (!empty($color->url_top)) { ?>
				top bouton view :<?php echo $product_path.'/normal/'.$color->url_top; ?><br><br>
			<?php } ?>
			associated products :<br>
			<?php foreach($color->node_associated as $assoc) { ?>
				<form action="<?php echo $assoc->macro_path;?>" method=post>
					<input type="hidden" value="<?php echo $assoc->title;?>">
					url : <?php echo $assoc->macro_path;?><br>
					parameter : <?php echo $assoc->title;?><br>
				</form>
			<?php } ?>
			<br>
		<?php } ?>
		
		<?php if(($field_ssc[0]['nid'])||($field_altium[0]['nid'])) {?>
			logos :<br>
			<?php if($field_ssc[0]['nid']) echo 'ssc logo : '.$theme_images.'/logos/ssc.gif'; ?><br>
			<?php if($field_altium[0]['nid']) echo 'altium logo : '.$theme_images.'/logos/altium.gif'; ?><br>
		<?php } ?>
		title :<?php echo check_plain($title);?><br>
		usp :<?php echo check_plain($field_usp[0]['value']); ?><br>
		description : <?php echo check_plain($node->content['body']['#value']); ?><br>
		<?php if(!empty($field_killerpointmacrolb[0]['value'])) { ?>
			key benefits:<br>
			<?php foreach($field_killerpointmacrolb as $i => $killer) { ?>
				<?php if(!empty($killer['value'])) { ?>
					title <?php echo $i ?> : <?php echo check_plain($killer['value']); ?><br>
					description <?php echo $i ?> : <?php echo check_plain($field_kcbarglb[$i]['value']); ?><br>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<?php 
			if(!empty($field_weight[0]['value']))
				echo t('Weight:'). ' ' . $field_weight[0]['value'].'<br />';
		?>
		<?php 
			if(!empty($list_color[$default_color]->field_size[0]['value'])) {
				echo t('Size: ').$list_color[$default_color]->field_size[0]['value'].' ('.t('UK').')';
			}
		?>
		<br>
		<?php if(!empty($field_technologienode[0]['nid'])) { ?>
			technologies:<br>
			<?php foreach($field_technologienode as $techno) { ?>
				<?php echo $techno['view'];?>
			<?php } ?>
		<?php } ?>
		<?php 
			if(!empty($field_featurenode[0]['nid'])) { 
				global $typeTechno;
				$listFeature = array();
				foreach($field_featurenode as $key => $feature) {
					$listFeature[$typeTechno[$key]][] = $feature;
				}
		?>
				features:<br>
				<?php foreach($listFeature as $type => $sublistfeature) { ?>
					type : <?php echo $type; ?> : <br>
					<?php foreach($sublistfeature as $feature) {?>
						<?php echo $feature['view'];?>
					<?php } ?>
				<?php } ?>
		<?php } ?>
	</div>

<br>fin : node-macromodel.tpl.php<br>