<?php if(!empty($breadcrumb[0]['link']['title'])) { ?>
	<div id="breadcrumb">
		<?php echo l($breadcrumb[0]['link']['title'],$breadcrumb[0]['link']['href']); ?>
		<?php switch($node->type) {
			case 'family' :
		?>
				
				> <select id="select_page">
					<?php 
						foreach($primary_links as $line) {
							foreach ($line['below'] as $category) {
								$family = @reset($category['below']);
								if($category['link']['in_active_trail']) $selected = " selected "; else $selected = "";
								echo '<option value="'.url($family['link']['href']).'"'.$selected.'>'.$line['link']['title'].'&nbsp;-&nbsp;'.$category['link']['title']."</option>\n";
							}
						}
					?>
				</select>
		<?php
			break;
			case 'macromodel' :
				echo ' > ' . l($breadcrumb[1]['link']['title'] .' - '. $breadcrumb[2]['link']['title'],$breadcrumb[3]['link']['href']);
		?>
				> <select id="select_page">
					<?php 
						foreach($breadcrumb[3]['below'] as $product) {
							if($product['link']['in_active_trail']) $selected = " selected "; else $selected = "";
							echo '<option value="'.url($product['link']['href']).'"'.$selected.'>'.$product['link']['title']."</option>\n";
						}
					?>
				</select>
		<?php
			break;
			default :
				$nbBread = count($breadcrumb);
				for($i = 1; $i < $nbBread; $i++) {
					echo ' > '. l($breadcrumb[$i]['link']['title'],$breadcrumb[$i]['link']['href']);
				}
			break;
		} ?>
	</div>
<?php } ?>