<div id="tabs_buttons_gamme">

<?php
	$output = ""; // to store all menu (needed to compute if "all news" menu is active)
	$all_news_active = 'tab active';
	foreach($menu_news as $data)
	{
		$classes = 'tab';
		if ($data['link']['in_active_trail']) 
		{
			$all_news_active = 'tab'; // if another menu is active, deactivate all_news
			$classes.= " active";
		}
		$output .='<div id="'. $data['link']['title'].'" onmouseout="outTab(this)" onmouseover="overTab(this)" class="'.$classes.'">';
		if($data['link']['expanded']) {
			$first = reset($data['below']);
			$link = $first['link']['href'];
		} else {
			$link = $data['link']['href'];
		}
		$output .='<a class="helvetica"  href="'.url($link).'" >';
		$output .=mb_strtoupper($data['link']['title']);
		$output .='</a>';
		$output .='</div>';
	}
?>

<div id="ALL_NEWS" onmouseout="outTab(this)" onmouseover="overTab(this)" class="<?php echo $all_news_active ?>">
	<a class="helvetica"  href="<?php echo url('news/all-news');?>" ><?php print(mb_strtoupper(t('ALL NEWS'))); ?></a>
</div>


<?php
	echo $output; // display all menu after "all news" menu
	global $language; 
	if($language->language == 'ja'):?>
	<div id="<?php echo t('link japan')?>" 
	onmouseout="outTab(this)" 
	onmouseover="overTab(this)" 
	class="tab">
	<a class="helvetica"  href="http://www.mavic.jp/news/" ><?php print(mb_strtoupper(t('Local News'))); //$data['href']	?></a>
	</div>
<?php endif;?>
</div>
<div class="clear"></div>
