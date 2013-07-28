<div class="element">
	<?php 
		if($field_technologie[0]['value'] == 2) { 
			$url = url($node->path);
		} else {
			// on recupere le pere
			$url = url('node/'.db_result(db_query('select n.nid from {content_field_child} n where n.field_child_nid='.$nid)));
		}
                $childFeatureCodeFound = '';
		foreach($field_feature_codes as $ChildFeatureCodeValue){
			$brodela = $technologies_path.'/brother/'.$ChildFeatureCodeValue["value"].'.jpg';
			if( file_exists( $technologies_path_sys.'/brother/'.$ChildFeatureCodeValue["value"].'.jpg')){
				$childFeatureCodeFound = $ChildFeatureCodeValue['value'];
			break;
			} 
		}
	?>
	<a href="<?php echo $url;?>" ><img src="<?php echo $technologies_path.'/brother/'.$childFeatureCodeFound.'.jpg'; ?>" class="big" alt="<?php print $title; ?>" /></a>
	<p class="title"><?php print $title; ?></p>
	<p class="text">
		<?php print truncate_utf8($node->content['body']['#value'], 190, true, true); ?>
	</p>
	<p class="more">
		<a href="<?php echo $url;?>" ><img border="0" src="<?php echo base_path().path_to_theme();?>/images/more_info.gif" alt="" /></a>
		<a href="<?php echo $url;?>" class="moreinfos"><?php echo t('More infos')?></a>
	</p>
	<div class="clear"></div>
</div>
