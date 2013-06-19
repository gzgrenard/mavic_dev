
<?php
		switch($node->type) {
		case 'landing_page_ss2011':
			print $content;
			exit;
	}
?>
<?php require('header.php'); ?>
<?php
	switch($node->type) {
		case 'album':
		case 'athlete':
			include('lightbox.php');
	}
?>
	<?php include('top_bar_menu.php') ?>
	<div id="container">
		<div id="black_screen" ></div>
		<div id="subcontainer">
			<?php include('productcompare.php'); ?>
			<?php $menufile = ((empty($discipline)) ? ('menu.php') : ('menu_disc.php'));
					include($menufile); ?>

			<div id="main_content">

				<?php include('breadcrumb.tpl.php') ?>

				<?php /*Show error messages for webform usage */ ?>
				<div id="error-messages" style="display:none"><?php echo $messages; ?></div>
				
				
				<?php if($variables['template_files'][0]=='page-search') :?>
					<div id="mavic_search">
						<h1 class="helvetica"><?php if ($_REQUEST['tpl_404']) { 
							print t('Sorry,').'<br />'. t('the page you are searching for cannot be found');
						} else { print t('Search'); }?></h1>
				<?php endif;?>
					<?php print $content; ?>
				
				<?php if($variables['template_files'][0]=='page-search') :?>
					</div>
				<?php endif;?>
				<?php if($variables['template_files'][0]=='page-search') :?>
				<script>
						$(document).ready(function() {
								var originalHeight =  $("#mavic_search").height();
								function changePageHeight() {
										// Si on se trouve sur la page 404, on force le bas de la page à se caller sur le bas du logo mavic
										var newHeight = ($('#logo_container').offset().top + $('#logo_container').height()) - $('#mavic_search').offset().top;
										if($('#mavic_search').height() < newHeight || (newHeight >= originalHeight && newHeight < $('#mavic_search').height())) $('#mavic_search').css('height', newHeight);
								}

								$(window).bind("resize", function () {changePageHeight()});
								changePageHeight();
						});
				</script>
				<?php endif;?>
				
			</div>
		</div>
		<!-- encart -->
		<div id="right_content_container">
			<div id="right_content">
<?php
				switch($node->type) {
					case 'athlete' :
					case 'prodvalcarac' :
					case 'news' :
					case 'album' :
					case 'history' :
						$item = menu_get_item($breadcrumb[1]['link']['href']);
						$parent_node = $item['map'][1];
						if(!empty($parent_node->field_encart[0]['nid']))
							foreach($parent_node->field_encart as $encart) 
								if(!empty($encart['nid']))
									echo node_view(node_load($encart['nid'])); 
					break;
					case 'macromodel' :
					case 'family' :
						$item = menu_get_item($breadcrumb[2]['link']['href']);
						$parent_node = $item['map'][1];
						if(!empty($parent_node->field_encart[0]['nid']))
							foreach($parent_node->field_encart as $encart) 
								if(!empty($encart['nid']))
									echo node_view(node_load($encart['nid'])); 
					break;
					default :
						if($variables['template_files'][1] == 'page-news-all-news') {
							$content_encart = node_load(substr(drupal_get_normal_path('all_news_generic'),5));
							if(!empty($content_encart->field_encart[0]['nid']))
								foreach($content_encart->field_encart as $encart) 
									if(!empty($encart['nid']))
										echo node_view(node_load($encart['nid'])); 
						} else {
							if(!empty($node->field_encart[0]['nid']))
								foreach($node->field_encart as $encart) echo $encart['view'];
						}
					break;
				}
				
				if(in_array('page-search-google_cse_adv', $template_files) && ($_REQUEST['tpl_404'])){
						// Encart pour la page 404
						$result = db_query("SELECT nid FROM {node} WHERE type = 'page_404' AND language = '" . $language->language . "'");
						if($result && count($result) >= 1 )
								$result = db_fetch_object($result);
						$content_encart = node_load($result->nid);
						if(!empty($content_encart->field_encart[0]['nid']))
						foreach($content_encart->field_encart as $encart) 
								if(!empty($encart['nid'])) echo node_view(node_load($encart['nid']));
				}
?>
			</div>
		</div>
		<!-- /encart -->
		
		<div id="forScrollTop" class="clear"></div>
	</div><!-- container -->

	<div id="logo_container">
		<?php echo l($breadcrumb[0]['link']['title'],$breadcrumb[0]['link']['href'], array('attributes' => array('id' => 'logo', 'title' => $breadcrumb[0]['link']['title']))); ?>
	</div>
<?php if(isset($_GET[nlfirstvisit])) : ?>
	<div id="nlfirstvisit">
		<a href="/<?php print $language->language ?>/newsletter/">
			<p  class="nlfirstvisit"><span class="helvetica"><?php print t('subscribe') ?></span><br /><span class="helvetica"><?php print t('to the Mavic') ?></span><br /><span class="helvetica"><?php print t('newsletter') ?></span></p>
		</a>
	</div>
<?php endif ?>
	<div id="footer">
		<?php print $footer; ?>
	</div>
	<div id="body-background"><img src="<?php if ($mobile == 'desktop') {echo $landscape; } else {echo $theme_path.'/images/bg_1px_7f.gif';}?>?v=1" width="1680" height="930" alt="Bg"></div>
<?php require("footer.php"); 