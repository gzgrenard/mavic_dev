<div class="right">

<?php global $language;
  
  $path = drupal_is_front_page() ? '<front>' : $_GET['q'];
  $languages = language_list('enabled');
  $options = array();
  foreach ($languages[1] as $lang_item) {
	$options[$lang_item->language] = array(
	  'href'       => $path,
	  'title'      => $lang_item->native,
	  'language'   => $lang_item,
	);
  }
  drupal_alter('translation_link', $options, $path);
  /* Here we theme our own dropdown */
  
?>
<div id="language-select-list">
	<div id="selected_langue" onclick="show_hide_select('#list_select_langue')"><?php echo $options[$language->language]['title']; ?></div>
	<ul id="list_select_langue">
		<?php 
			foreach($options as $key_lang => $lang_option) {
				$path = check_url(url($lang_option['href'], array('language' => $lang_option['language'])));
				if($language->language != $key_lang)
					echo '<li><a href="'.$path.'">'. $lang_option['title']."</a></li>";
				else 
					echo '<li><a class="active" href="'.$path.'">'. $lang_option['title']."</a></li>";
			}
		?>
	</ul>
	<script type="text/javascript">
		$('#body').click( function(){ $('#list_select_langue').css('display','none'); });
	</script>
</div>

</div>
<div class="clear"></div>