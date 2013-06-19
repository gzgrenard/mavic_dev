<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
	});
</script>
<div id="tabs">
	<?php require_once('node-technoline-tabs.tpl.php'); ?>
	<br>
	<br>
	<ul id="techno_list_cat_big">
		<?php
			foreach($breadcrumb[1]['below'] as $product) {
				$item = menu_get_item($product['link']['href']);
				$itemMap = $item['map'][1];
				$child = reset($product['below']);
				$link = url($child['link']['href']);
		?>
				<li class="li_cat">
					<a href="<?php echo $link; ?>">
						<img class="img_cat" src="<?php echo $technologies_path.'/categories/big/'.$itemMap->body; ?>.jpg" alt="<?php echo $itemMap->title; ?>" align="left" />
						<h1 class="helvetica"><?php echo $itemMap->title ?></h1>
						<div><?php echo $itemMap->field_description[0]['value']; ?></div>
						<img class="plus_cat" src="<?php echo base_path().path_to_theme();?>/images/more_info.gif" alt="" align="left" />
						<div class="cat_link">
							<?php echo t('See the technologies @cat ',array('@cat'=>$itemMap->title));?>
						</div>
						<div class="clear"></div>
					</a>
				</li>
		<?php
			}
		?>
	</ul>
</div>



