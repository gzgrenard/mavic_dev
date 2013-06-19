<div id="tabs_buttons_gamme" class="family-page">
	<?php 
		
			foreach($primary_links as $line) {
				if ($line['link']['title'] == mb_strtolower($breadcrumb[1]['link']['title'])) {
					foreach ($line['below'] as $category) {
						$family = @reset($category['below']);
						$class = '';
						$href = url($family['link']['href']);
						$title_bread = $category['link']['title'];
						if ($category['link']['in_active_trail']) {
							$class = 'active';
						}
						echo '<div onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab '. $class .
								'"><a class="helvetica" href="'. $href.
								'">'. $title_bread .
								'</a></div>';
					}
				}
			}
		
	?>
	</div>
<div class="clear"></div>

