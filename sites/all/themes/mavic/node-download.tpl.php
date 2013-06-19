	<div class="download_content">
		<?php
			if(!empty($field_download_url[0]['value'])) {
				$url = $field_download_url[0]['value'];
				$size = '';
			} else {
				$url = 'http://www.mavic.com/' . $field_download_file[0]['filepath'];
				$size = '('.round($field_download_file[0]['filesize']/1048576,2).' mo)';
			}
			if(!empty($field_download_picto[0]['value'])) $picto =  base_path() . $field_download_picto[0]['value'];
			else $picto = $theme_images.'/'.$field_download_type[0]['value'];
		?>
		<img src="<?php echo $picto; ?>" />
		<a href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a>
		<span class="size"><?php echo $size;?></span>
	</div>



