<div id="nextprevmacro">
			<div id="1select_page" class="select_page">
					<?php 
						foreach($primary_links as $line) {
							if($line['link']['in_active_trail']){
								foreach ($line['below'] as $category) {
								$family = @reset($category['below']);
								if($category['link']['in_active_trail'])
									echo '<div class="backtolist"><a href="'.url($family['link']['href']).'">'. t('complete range').'</a></div>';
								}
							}
						}
						
					?>
			</div>
		<?php 
			$stopprev = false;
			$stopnext = true;
			$prevart = '';
			$nextart = '';
			foreach($breadcrumb[3]['below'] as $product) {
				if(!$product['link']['in_active_trail'] && $stopnext == false){
					$stopnext = true;
					$nextart = url($product['link']['href']);
				
				}
				if($product['link']['in_active_trail']){
					$stopprev = true;
					$stopnext = false;
				}
				if(!$product['link']['in_active_trail'] && $stopprev == false){
					$prevart = url($product['link']['href']);
				
				}
			}
		?>
		<?php if (!empty($nextart)) : ?><div class="nextious"><a href="<?php echo $nextart; ?>"><?php echo t('next product'); ?></a></div><?php endif ?>
		<?php if (!empty($prevart) && !empty($nextart)) : ?><div class="separator"></div><?php endif ?>
		<?php if (!empty($prevart)) : ?><div class="previous"><a href="<?php echo $prevart; ?>"><?php echo t('previous product'); ?></a></div><?php endif ?>

</div>

