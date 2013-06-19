<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
	var theme_path = '<?php echo base_path().path_to_theme();?>';
</script>
<div id="tabs">
	<script src="<?php echo base_path().path_to_theme();?>/js/api_flickr.js"></script>

<?php
	require_once('node-album-tabs.tpl.php');
?>
	<br class='clear' />
<?php
		$i = $j = 0;
			foreach($breadcrumb[1]['below'] as $product) {
				$item = menu_get_item($product['link']['href']);
				$itemMap = $item['map'][1];
				$link = url($product['link']['href']);
				if($product['link']['in_active_trail']) $classe = ' techno_brother_actif'; else $classe = '';
				if($i == 2) {
					$classe .= ' third_brother_techno';
					$i = 0;
				} else {
					$i++;
				}
?>
				<a href="<?php echo $link?>" class="techno_brother<?php echo $classe ?>">
					<img src="<?php echo base_path().$itemMap->field_img_album[0]['filepath'] ?>" />
					<div id="album_<?php echo $j;?>" class="helvetica techno_brother_title"><?php echo $product['link']['title'] ?></div>
				</a>
				<script type="text/javascript">
					flickr_gallery.getSetSize('<?php echo $itemMap->field_flickr_set[0]['value']; ?>', 
						function(data) {
							$elem = document.getElementById('album_<?php echo $j;?>');
							$elem.innerHTML = '<?php echo $product['link']['title'] ?> (' + data.photoset.photos + ')';
						}
					);
				</script>
<?php
				if( $i == 0 ){
?>
					<div class="clear"></div>
<?php
				}
				$j++;
			}
?>
	<div style="clear: both"></div>
	<!-- <br class='clear' /> -->
	
	<!-- thumbnail -->
	<div id="gallery"></div>
	
	<!-- load data from flickr for gallery and lightbox-->
	<script type="text/javascript">
		flickr_gallery.getSet('<?php echo $node->field_flickr_set[0]['value']; ?>', function(data) {display_set(data);});
	</script>
	
</div>