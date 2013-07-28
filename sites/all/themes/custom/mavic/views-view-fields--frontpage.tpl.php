<?php
	$node=node_load($fields['nid']->raw);
	$tabnidlang = translation_node_get_translations($node->tnid);
	
	if (strpos($fields['field_url_value']->raw, 'http://') === 0) {
		$link = $fields['field_url_value']->raw;
	} else {
		$link = base_path().$fields['field_url_value']->raw;
		if (!empty($fields['title']->raw)){
			$tmp = explode('#',$link);
			$tmp[0] .= ((strpos($tmp[0],'?')===FALSE)?'?':'&').'intcmp='.urlencode('homehighlight_'.preg_replace('/[^a-z0-9]+/i','_',$tabnidlang['en']->title));
			(count($tmp)>1)?($tmp[0] .= '_'.$tmp[1]):0;
			$link = implode('#',$tmp);
		}
		
	}

?>
<div class="homeslide" id="homeslide_<?php echo $fields['field_system_name_value']->raw;?>" > 
	
	<img 
		class="img"
		id="img_<?php echo $fields['field_system_name_value']->raw;?>"
		src="<?php echo base_path().'sites/default/files/homepage/'.$fields['field_image_value']->raw ?>?v3"
		<?php if(($fields['field_system_name_value']->raw == "ecard2013vtt") || ($fields['field_system_name_value']->raw == "ecard2013road") || ($fields['field_system_name_value']->raw == "ironman") || ($fields['field_system_name_value']->raw == "contest") || ($fields['field_system_name_value']->raw == "giro_home") || ($fields['field_system_name_value']->raw == "london") || ($fields['field_system_name_value']->raw == "TDF_2012") || ($fields['field_system_name_value']->raw == "enduro_barel")){
			echo 'width="1680" height="930"';
		} else {
			echo 'width="1200" height="1200"';
		} ?>
	/>

	<div 
		class="homedescription" 
		id="homedescription_<?php echo $fields['field_system_name_value']->raw;?>"
	>
	<?php if($field_ssc[0]['value']) $imgs[] = '<img src="'.$theme_images.'/logos/ssc.gif" />';?>
		<h1 class="helvetica title_product" >
			<?php $myTitle = explode(' | ',$fields['title']->raw);
				if(count($myTitle)>1){
					echo '<span>'.implode('</span><br /><span class="nextline">',$myTitle).'</span>';
				}
				else{
					echo $myTitle[0];
				}
				if($fields['field_ssc_value']->raw):?>
				<img src="<?php echo $theme_images;?>/logos/ssc_product.png" />
			<?php endif;?>
		</h1>
		<div class="helvetica" >
			<?php echo nl2br($fields['body']->raw) ?>
		</div>
		<a href="<?php echo $link; ?>" class="homemore" ><?php if($fields['field_system_name_value']->raw == "contest"){echo t('PARTICIPATE');} else {echo t('MORE');}?></a>
		
	</div>
	
</div>

<script>
	slides[slides.length]='<?php echo $fields['field_system_name_value']->raw;?>';
</script>