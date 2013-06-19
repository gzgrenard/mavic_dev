<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		$('#main_content').css('height','auto');
	});
</script>
<div id="tabs" class="careers-content-container">
	<div id="tabs_buttons" class="careers-paragraph right-col">
	<?php 
		$stopprev = false;
		$stopnext = true;
		$nextious = '';
		$previous = '';
		
		global $language;
		$menu_careers = (isset($menu_careers)) ? $menu_careers : menu_tree_page_data('menu-careers');
		$currentJobUrl = "";
			foreach ($menu_careers as $job){
				$linkIsDisplayed = true;
				if($job[link][href] == 'careers') {
					$currentJobUrl = 'node/'.$node->nid;
					$linkIsDisplayed = false;
				} else {
					$linkIsDisplayed = ($job[link][localized_options][langcode] != $language->language) ? false : true;
				}
				
				if($linkIsDisplayed == true)
				{
					$class = '';
					$href = url($job[link][href]);
					if(!($job['link']['in_active_trail'] || $currentJobUrl == $job[link][href] ) && $stopnext == false){
						$stopnext = true;
						$nextious = $href;
					}
					if (($job[link][in_active_trail] || $currentJobUrl == $job[link][href])) {
						$class = 'active';
						$href = ( $currentJobUrl == $job[link][href]) ? url($job[link][href])  : '#';
						$stopprev = true;
						$stopnext = false;
					}
					if(!($job['link']['in_active_trail'] || $currentJobUrl == $job[link][href] ) && $stopprev == false){
						$previous = $href;
					
					}
					$out = '<div onmouseout="outTab(this)" onmouseover="overTab(this)" onclick="$(location).attr(\'href\',$(this).children().attr(\'href\'));"class="tab '. $class .
							'"><a href="'. $href.
							'"><b>'. $job['link']['title'].'</b>';
					if($job['link']['options']['attributes']['title'] ) $out .='<br /><span class="linkDesc">'. $job['link']['options']['attributes']['title']  .'</span>';
					$out .=		'</a></div>';
					
					echo $out;
				}
			}
	?>
				<div class="tab no_pointer"><b><?php echo t('General inquiries')?></b>
				<br /><div class="linkDesc"><?php echo t('If you are interested in working at Mavic please e-mail your resume and cover letter in PDF or Word format ') ?><b><a href="mailto:HRServices@mavic.com"><?php echo t('by clicking here.') ?></a></b> <?php echo t('Include the position or type of position you are interested in applying for in the subject line.')?>
				</div></div>
	</div>
	<div id="title">
		<h1 class="helvetica"><?php echo $title;?> </h1><br />
		<?php if($field_careers_location[0]['value']) :?>
			<span class="job_location helvetica">(<?php echo $field_careers_location[0]['value'];?>)</span>	
		<?php endif;?>
	</div>
	<div>
		<?php if ($field_careers_intro[0]['value']) : ?>
		<div class="careers-paragraph left">
				<span class="careers-line"><h2><?php echo t('Presentation of the Company')?></h2></span>
				<?php echo $field_careers_intro[0]['value'];?>		
		</div>
		<?php endif;?>
		<?php if ($node->content['body']['#value']) : ?>
		<div class="careers-paragraph left">
				<span class="careers-line"><h2><?php echo t('Description')?></h2></span>
				<?php echo $node->content['body']['#value'];?>		
		</div>
		<?php endif;?>
		<?php if ($field_careers_responsability[0]['value']) : ?>
		<div class="careers-paragraph left">
			<span class="careers-line"><h2><?php echo t('Responsabilities')?></h2></span>
			<?php echo $field_careers_responsability[0]['value'];?>		
		</div>
		<?php endif;?>
		<?php if ($field_careers_profil[0]['value']) : ?>
		<div class="careers-paragraph left">
				<span class="careers-line"><h2><?php echo t('Profile')?></h2></span>
				<?php echo $field_careers_profil[0]['value'];?>
		</div>
		<?php endif;?>
		<?php if ($field_careers_contract[0]['value']) : ?>
		<div class="careers-paragraph left">
			<span class="careers-line"><h2><?php echo t('Contract type ');?></h2></span>
			<?php echo $field_careers_contract[0]['value']; ?>
		</div>
		<?php endif;?>
		<?php if ($field_careers_entry[0]['value']) : ?>
		<div class="careers-paragraph left">
			<span class="careers-line"><h2><?php echo t('Prefered entry date ');?></h2></span>
			<?php echo $field_careers_entry[0]['value']; ?>
		</div>
		<?php endif;?>
		<?php if ($node->content['body']['#value']) : ?>
		<div class="careers-paragraph left">
			<?php
			$out = '<span class="careers-line"><h2>'.t('To apply').'</h2></span>';
			$out .= t('If this sounds like you, then please apply by forwarding your resume and cover letter in PDF or Word format ');
			$mailObjetct = t('application').' : '.$title;
			$out .= '<a href="mailto:HRServices@mavic.com?subject='.$mailObjetct.'" ><b>';
			$out .= t('by clicking here.'); 
			$out .= '</a></b>';
			echo $out; ?>
		</div>
		<?php endif;?>
	</div>
	<br class='clear' />
	<div id="careers_prev_next">
		<?php
			
			//
			// to have english version for omniture
			//
			if($tnid != $nid) {
				$english_node = node_load($tnid);
			} else {
				$english_node = $node;
			}
		?>
				<script>
					$('.apply').click(function(e){
						omniture_click(this, 'Apply job:<?php echo $english_node->title;?>');
					
					});
					$('.apply').css({'cursor':'pointer'});
				</script>
	</div>
</div>


