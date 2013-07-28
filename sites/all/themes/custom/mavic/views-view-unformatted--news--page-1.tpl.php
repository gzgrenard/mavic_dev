<?php 
	
	global $list_all_news; // to restore all news save in views-view-field-news template
	//var_dump($view);
	$i = 0;
	foreach($list_all_news as $product) {
		if($i == 0){
			$classe = 'class="news-content-container"';
		} else {
			$classe = 'class="news-content-container" style="display:none"';
		}
			//
			// to have english version for omniture
			//
			if($product['nid'] != $view->result[$i]->node_tnid) {
				$english_node = node_load($view->result[$i]->node_tnid);
				$english_name = $english_node->title;
			} else {
				$english_name = $product['title']->raw;
			}
		
?>
		<div id="news_content_<?php echo $i ?>" <?php echo $classe; ?>>
			<h1 class="helvetica"><?php echo $product['title']->raw;?> </h1>
			<span class="date">(<?php echo $product['date'] ?>)</span>
			<div class="clear"></div>
			<p class="news-paragraph">
				<img alt="<?php echo $product['title']->raw;?>" src="<?php echo str_replace('.jpg','_m.jpg',$product['field_news_picture_flickr_value']->raw)?>" class="imagefield" />
				<?php echo $product['body']->raw;?>
			</p>
			<div class="clear"></div>
			<?php if(!empty($product['field_news_product_nid']->content)):?>
				<div class="associated-links">
					<h2 class="small"><?php echo t('Associated products:')?></h2>
					<?php echo $product['field_news_product_nid']->content ?>
				</div>
			<?php endif;?>
			<?php if ($i==0){ ?>
			<!-- AddThis Button BEGIN -->
				<div id="addthis_container_0" class="addthis_toolbox news" addthis:url="<?php print url('node/'.$product['nid'], array('absolute'=>'true'));?>">
					<div class="custom_images">
						<a id="addthis_button_google_plusone_<?php print $i; ?>" class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
						<a id="addthis_button_twitter_<?php print $i; ?>" class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" />
						</a>
						<a id="addthis_button_facebook_<?php print $i; ?>" class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" />
						</a>
					</div>
				</div>
			<!-- AddThis Button END -->
			<?php } else { 
			?>
			<!-- AddThis Button BEGIN -->
			<div id="addthis_container_<?php print $i; ?>" class="news" alt="<?php print url('node/'.$product['nid'], array('absolute'=>'true'));//$product['path']?>"></div>
			<!-- AddThis Button END -->
			<?php } ?>
		</div>
		
<?php 
		$i++;
	} 
?>
<script>
	current_news = 0;
	total_news = <?php echo $i-1 ?>;
	
	function goPreviousNews() {
		current = $("#news_content_"+current_news);
		current_brother = $("#brother_news_"+current_news);
		$("#addthis_container_"+current_news).html('');
		current_news--;
		processBtnNews(current,current_brother);
		$("#bt_next_news").show();
		if(current_news == 0) $("#bt_previous_news").hide();
	}
	
	function goNextNews() {
		current = $("#news_content_"+current_news);
		current_brother = $("#brother_news_"+current_news);
		$("#addthis_container_"+current_news).html('');
		current_news++;
		processBtnNews(current,current_brother);
		$("#bt_previous_news").show();
		if(current_news == total_news) $("#bt_next_news").hide();
	}
	function processBtnNews(current,current_brother){
		var new_adt = $("#addthis_container_"+current_news);
		var adtContent = "<div class=\"custom_images\"><a id=\"addthis_button_google_plusone_" + current_news + "\" class=\"addthis_button_google_plusone\" g:plusone:annotation=\"none\"></a><a id=\"addthis_button_twitter_" + current_news + "\" class=\"addthis_button_twitter\"><img src=\"<?php echo base_path().path_to_theme();?>/images/share/tweet.gif\" height=\"14\" border=\"0\" /></a><a id=\"addthis_button_facebook_" + current_news + "\" class=\"addthis_button_facebook\"><img src=\"<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif\" height=\"14\" border=\"0\" /></a></div>";
		if (current_news > 0){
			if(!new_adt.hasClass('addthis_toolbox')) {
				new_adt.addClass('addthis_toolbox').attr('addthis:url', new_adt.attr('alt'));
				new_adt.html(adtContent);
				addthis.toolbox("#addthis_container_"+current_news);
			}
		}
		new_item = $("#news_content_"+current_news);
		new_brother = $("#brother_news_"+current_news);
		current.hide();
		new_item.show();
		current_brother.removeClass('news_item_active');
		new_brother.addClass('news_item_active');
	}
</script>

<br class='clear' />
<div id="news_prev_next">
	<a id="bt_previous_news" class="button_view button-view-previous" style="display:none" href="#" onclick="goPreviousNews()"><?php echo t('Previous news') ?></a>
	<?php
		if(!empty($list_all_news[1])) {
			echo ' <a id="bt_next_news" class="button_view button-view-next" href="#" onclick="goNextNews()">'.t('Next news').'</a>';
		}
	?>
	<p class="actions">				
		<img src="<?php echo base_path().path_to_theme();?>/images/print.gif" alt="" class="print" />
		<a href="javascript: window.print()" onclick="omniture_click(this, 'Print:<?php echo $english_node->title;?>');" id="printLink"><?php print (t('PRINT'))?></a>					
	</p>
</div>
		
<div class="view-news-page" id="list_morenews"  >
&nbsp;
</div>
<div class="morenews" >
		<a href="javascript:showMoreNews(10);" class="button_view" ><?php echo t('More news'); ?></a>
</div>
<br/>
<div id="list_nextnews" style="display:none;">
	<?php
		$i = 0;
		foreach($list_all_news as $product) {
			if($i == 0)
				$classe = 'active'; 
			else 
				$classe = '';
	?>
			<div id="brother_news_<?php echo $i ?>" class="news-preview news_item_<?php echo $classe?>" onclick="document.location.href='<?php echo $product['path']?>'" >
					<div class="imageslot">
						<img height="108" src="<?php echo str_replace('.jpg','_m.jpg',$product['field_news_picture_flickr_value']->raw)?>" class="imagefield" />
					</div>
					<div class="contentslot">
							<h2><?php echo $product['title']->raw ?></h2>
							<p><?php echo truncate_utf8($product['field_news_intro_value']->raw, 190, true, true);?> <span class="news_date_brother">(<?php echo $product['date'] ?></span>)</p>
							<img src="/sites/default/themes/mavic/images/more_info.gif" alt=""/>
							<a href="<?php echo $product['path']?>" class="news-more-info">
							<?php echo t('More info');?>
							</a>
					</div>
					<div style="clear: both"></div>
			</div>
	<?php
			$i++;
		}
	?>
</div>



