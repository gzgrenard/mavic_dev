<div id="right_content_container">
    <div id="right_content">
		<?php switch($language->language) //to finish with definitive content
			{
				case 'ja':
        		$linktoassistance = '/'.$language->language.'/'.str_replace(array('&','?','='),'', t('assistance') );	
				?>
				<a class="link" href="<?php echo $linktoassistance; ?>">
					<div class="text">
						<p class="helvetica title">		
								<?php print t('assistance') ?>	
						</p>
					</div>
					<img src="<?php echo base_path().path_to_theme();?>/images/banners/assistance.jpg" alt="image1" />
					
				</a>
				<?php 
				break;
				default:
				?>
		    	<a class="link" href="http://www.maviclab.com" target="_blank">
					<div class="text">
						<p class="helvetica title"><?php print t('Mavic Lab') ?></p>
						<p class="content"><?php echo t('configure your own wheels')?></p>
					</div>
					<img style="float:left;" src="<?php echo base_path().path_to_theme();?>/images/banners/maviclab.jpg" alt="image1" />
				</a>
				<?php 
			}
		?>
		<div class="clear"></div>
		<a class="link" href="<?php echo url('news/all-news') ?>">
			<div class="text">
				<p class="helvetica title">			
						<?php print t('News') ?>
				</p>
			</div>
			<img src="<?php echo base_path().path_to_theme();?>/images/banners/news.jpg" alt="image1" />
		</a>
		<div class="clear"></div>
		<?php if($node->type != 'video') { ?>
			<div class="flash_content" id="player">
				<img src="<?php echo base_path().path_to_theme();?>/images/flash_right_content.jpg" alt="un flash ici plus tard" />
			</div>
		<?php } ?>
	</div>
</div>