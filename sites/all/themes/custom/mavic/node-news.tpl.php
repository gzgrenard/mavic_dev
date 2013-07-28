<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		showMoreNews(6);
		checkSize();
		$('#main_content').css('height','auto');
	});
</script>
<div id="tabs" class="news-page">
<?php 
	if (empty($discipline)) require_once('node-news-tabs.tpl.php'); 
	$depth = count($breadcrumb);
	
	$trad = array();
	$trad['01'] = t('January');
	$trad['02'] = t('February');
	$trad['03'] = t('March');
	$trad['04'] = t('April');
	$trad['05'] = t('May');
	$trad['06'] = t('June');
	$trad['07'] = t('July');
	$trad['08'] = t('August');
	$trad['09'] = t('September');
	$trad['10'] = t('October');
	$trad['11'] = t('November');
	$trad['12'] = t('December');
?>
	<div class="news-content-container">
		<h1 class="helvetica"><?php echo $node->title;?> </h1>
		<?php
			$day = (int)substr($node->field_news_date[0]['value'],8,2);
			$month = $trad[substr($node->field_news_date[0]['value'],5,2)];
			$year = substr($node->field_news_date[0]['value'],0,4);
		?>
		<span class="date">(<?php echo $day.' '.$month.' '.$year ?>)</span>
		<div class="clear"></div>
		<p class="news-paragraph">
			<img alt="<?php echo $node->title;?>" src="<?php echo str_replace('.jpg','_m.jpg',$node->field_news_picture_flickr[0]['value'])?>" class="imagefield" />
			<?php echo $node->content['body']['#value'];?>
			
		</p>
		<div class="clear"></div>
		<?php if($node->field_news_product[0]['nid']):?>
			<div class="associated-links">
				<h2 class="small"><?php echo t('Associated products:')?></h2>
				
				<?php foreach($node->field_news_product as $product):?>
					<?php if(!empty($product['view'])) print $product['view'].'<br />' ?>
				<?php endforeach;?>
			</div>
		<?php endif;
			//
			// to have english version for omniture
			//
			if($tnid != $nid) {
				$english_node = node_load($tnid);
			} else {
				$english_node = $node;
			}
		?>
			<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox news">
					<div class="custom_images">
						<a class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
						<a class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" alt="<?php print t('Share to Twitter'); ?>" />
						</a>
						<a class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" alt="<?php print t('Share to Facebook'); ?>" />
						</a>
					</div>
				</div>
			<!-- AddThis Button END -->
			
	</div>
<?php
	$parent = $depth - 2;
	$elem = $depth - 1;
	$current = $breadcrumb[$elem]['key_breadcrumb'];
	reset($breadcrumb[$parent]['below']);
	$previous = '';
	while(key($breadcrumb[$parent]['below']) != $current) {
		$previous = current($breadcrumb[$parent]['below']);
		next($breadcrumb[$parent]['below']);
	}
	$next = next($breadcrumb[$parent]['below']);
?>
	<br class='clear' />
	<div id="news_prev_next">
		<?php
			if($previous != '') {
				echo '<a class="button_view button-view-previous" href="'.url($previous['link']['href']).'">'.t('Previous news').'</a>';
			}
			
			if($next) {
				echo ' <a class="button_view button-view-next" href="'.url($next['link']['href']).'">'.t('Next news').'</a>';
			}
			
		?>
		<p class="actions">				
			<img src="<?php echo base_path().path_to_theme();?>/images/print.gif" alt="" class="print" />
			<a href="javascript: window.print()" onclick="omniture_click(this, 'Print:<?php echo $english_node->title;?>');" id="printLink"><?php print (t('PRINT'))?></a>					
		</p>
	</div>
		
<?php
	if(count($breadcrumb[$parent]['below']) > 1) { // if more news
?>
		<div class="view-news-page" id="list_morenews"  >
			&nbsp;
		</div>
<?php 
		if(count($breadcrumb[$parent]['below']) > 10) { // if bt more news
?>
			<div class="morenews" >
				<a href="javascript:showMoreNews(10);" class="button_view" ><?php echo t('More news'); ?></a>
			</div>
<?php
		}
?>
		<br/>
		<div id="list_nextnews" style="display:none;">
<?php 
			foreach($breadcrumb[$parent]['below'] as $product) {
				
				$item = menu_get_item($product['link']['href']);
				$itemMap = $item['map'][1];
				
				if($product['link']['in_active_trail'])
				{
					$link = '#';
					$classe = 'active'; 
				}
				else 
				{
					$classe = '';
					$link = url($product['link']['href']);
				}
				$day = (int)substr($itemMap->field_news_date[0]['value'],8,2);
				$month = $trad[substr($itemMap->field_news_date[0]['value'],5,2)];
				$year = substr($itemMap->field_news_date[0]['value'],0,4);
?>
				<div class="news-preview news_item_<?php echo $classe?>" onclick="document.location.href='<?php echo $link?>'" >
					<div class="imageslot">
						<img height="108" src="<?php echo str_replace('.jpg','_m.jpg',$itemMap->field_news_picture_flickr[0]['value'])?>" class="imagefield" />
					</div>
					<div class="contentslot">
						<h2><?php echo $itemMap->title ?></h2>
						<p><?php echo truncate_utf8($itemMap->field_news_intro[0]['value'], 190, true, true);?> <span class="news_date_brother">(<?php echo $day.' '.$month.' '.$year ?></span>)</p>
						<img src="/sites/default/themes/mavic/images/more_info.gif" alt=""/>
						<a href="<?php echo $link?>" class="news-more-info">
						<?php echo t('More info');?>
						</a>
					</div>
					<div style="clear: both"></div>
				</div>
				
<?php
			}
?>
		</div>
<?php
	} // if more news
?>
</div>


