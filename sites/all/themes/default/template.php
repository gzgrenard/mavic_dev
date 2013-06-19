<?php

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess(&$vars,$hook) {
	
	static $menu_array, $active_menu_name, $description, $keywords, $head_title;
	global $breadcrumb;
	global $language;
	
	//
	// path for image, css, and js
	//
	
	if($vars['template_files'][0]=='page-productcompare')
	{
		//echo print_r($vars['template_files'][1]);
		$nid = explode('-',$vars['template_files'][1]);
		menu_set_active_item('node/'.$nid[2]);
	}
	
	$vars['theme_path']    = base_path() . path_to_theme();
	$vars['theme_images']  = $vars['theme_path'] . '/images';
	$vars['product_path']  = base_path() .file_directory_path().'/products';
	$vars['product_path_sys']  = file_directory_path().'/products';
	$vars['features_path'] = base_path() . file_directory_path().'/features';
	$vars['features_path_sys'] = file_directory_path().'/features';
	$vars['technologies_path'] = base_path() . file_directory_path().'/technologies';
	$vars['technologies_path_sys'] = file_directory_path().'/technologies';
	$vars['home_img_path'] = base_path() . file_directory_path().'/homepage';
	
	//
	// lang
	//
	if(!empty($vars['language']->language)) $vars['lang'] = $vars['language']->language;
	elseif(!empty($vars['language'])) $vars['lang'] = $vars['language'];
	else print_r($_GLOBALS);
	
	
	if(!$vars['is_admin'] && 
		($hook=="node" && $vars['page'] || $hook=="page") && 
		($vars['template_files'][0] != 'shopfinder-view')) {
		
		//
		// menu
		//
		if(empty($breadcrumb)) {
			if(!menu_get_item()) // on initialise le menu
				menu_set_item(NULL, menu_get_item('node'));
			$menu_array = array('primary_links'=>array('name'=>'menu-primary-links-'.$vars['lang']),
								'menu_video'=>array('name'=>'menu-videos'),
								'menu_photo'=>array('name'=>'menu-photo'),
								'menu_download'=>array('name'=>'menu-download'),
								'menu_technologies'=>array('name'=>'menu-technologies-'.$vars['lang']),
								'menu_history'=>array('name'=>'menu-history'),
								'menu_news'=>array('name'=>'menu-news'),
								'menu_bottom'=>array('name'=>'menu-bottom')
							   );
			foreach($menu_array as $key => $value) {
				$menu_array[$key]['menu'] = menu_tree_page_data($value['name']);
				if($breadcrumb_tmp = clean_menu($menu_array[$key]['menu'])) {
					$breadcrumb = $breadcrumb_tmp;
					$active_menu_name = $value['name'];
				}
			}
			
			if(empty($breadcrumb))
			{
				$breadcrumb[] = array('link'=>array('title' => t('home page'), 'href' => '<front>', 'localized_options' => array(), 'type' => 0));
			}
			
		}
		foreach($menu_array as $key => $value) $vars[$key] = $value['menu'];
		
		$vars['breadcrumb'] = $breadcrumb;
		$vars['active_menu_name'] = $active_menu_name;
		
		if($hook=="page") {

			$vars['head_title'] = $head_title;
			$vars['keywords'] = $keywords;
			$vars['description'] = $description;
			
			if(!$vars['is_front']) {
				
				//
				// landscape
				//
				if (empty($vars['node']->field_landscape)) {
					require drupal_get_path('module','mavicimport').'/parameters.php';
					$land_tab = $parameters['landscape']['all'];
				} else {
					$land_tab = $vars['node']->field_landscape;
				}
				$nbLand = count($land_tab);
				$vars['landscape'] = $vars['theme_images'].'/landscapes/'.$land_tab[rand(0,$nbLand-1)]['value'].'.jpg';
			}
			else
			{
				$vars['head_title'] = 'Mavic | '.t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
				$vars['description'] = t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
			}

			
		} elseif($hook=="node") { // node de page
			
			//
			// get article, associated article and parent macromodel
			//
			$keywords = $vars['node']->field_page_keyword[0]['value'];
			$description = $vars['node']->field_page_description[0]['value'];
			
			switch ($vars['type']) {
				case 'macromodel' :
					$vars['list_color'] = array();
					foreach($vars['field_otherarticle'] as $i => $article) {
						$tmp = node_load($article['nid']);
						$tmp->node_associated = array();
						foreach($tmp->field_associated as $color) {
							if(!empty($color['nid'])) {
								$assoc_node = node_load($color['nid']);
								$macro_nid_tab = db_fetch_object(db_query('SELECT o.nid, m.field_usp_value, n.title FROM {content_field_otherarticle} o INNER JOIN {content_type_macromodel} m using (nid) INNER JOIN {node} n using (nid) WHERE o.field_otherarticle_nid='. $color['nid']));
								$assoc_node->macro_path = url('node/'.$macro_nid_tab->nid);
								$assoc_node->macro_usp = $macro_nid_tab->field_usp_value;
								$assoc_node->macro_title = $macro_nid_tab->title;
								$tmp->node_associated[] = $assoc_node;
							}
						}
						$vars['list_color'][$tmp->title] = $tmp;
						$vars['list_color'][$tmp->title]->url_default  = $tmp->title.'.jpg';
						
						if(file_exists($vars['product_path_sys'].'/normal/'.$tmp->title.'_wheelsFront.jpg')) {			
							$vars['altview_label']   = array(t('front wheel'),t('rear wheel'));
							$vars['altview_image_suffix']   = '_wheelsFront.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title.'_wheelsFront.jpg';
						} 
						if(file_exists($vars['product_path_sys'].'/normal/'.$tmp->title.'_glovesBottom.jpg')) {
							$vars['altview_label']   = array(t('palm view'),t('top view'));
							$vars['altview_image_suffix']   = '_glovesBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview     = $tmp->title.'_glovesBottom.jpg';
						}
						if(file_exists($vars['product_path_sys'].'/normal/'.$tmp->title.'_pedalsSide.jpg')) {
							$vars['altview_label']   = array(t('lateral view'),t('top view'));
							$vars['altview_image_suffix']   = '_pedalsSide.jpg';
							$vars['list_color'][$tmp->title]->url_altview     = $tmp->title.'_pedalsSide.jpg';
						}
						if(file_exists($vars['product_path_sys'].'/normal/'.$tmp->title.'_footwearBottom.jpg')) {
							$vars['altview_label']   = array(t('outsole view'),t('top view'));
							$vars['altview_image_suffix']   = '_footwearBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview     = $tmp->title.'_footwearBottom.jpg';
						}
						if(file_exists($vars['product_path_sys'].'/normal/'.$tmp->title.'_bootiesBottom.jpg')) {
							$vars['altview_label']   = array(t('outsole view'),t('top view'));
							$vars['altview_image_suffix']   = '_bootiesBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview     = $tmp->title.'_bootiesBottom.jpg';
						}
					}
					if(!empty($vars['list_color'][$_POST['default_article']])) {
						$vars['default_color'] = $_POST['default_article'];
					} else {
						$vars['default_color'] = key($vars['list_color']);
					}
					if(mb_strtolower($breadcrumb[1]['link']['title']) == mb_strtolower($breadcrumb[2]['link']['title']))
						$head_title = $breadcrumb[4]['link']['title'] .' - '. $breadcrumb[1]['link']['title'] .' - Mavic';
					else
						$head_title = $breadcrumb[4]['link']['title'] .' - '. $breadcrumb[1]['link']['title'] .' - '. $breadcrumb[2]['link']['title'].' - Mavic';
				break;
				case 'family' :
					if(mb_strtolower($breadcrumb[1]['link']['title']) == mb_strtolower($breadcrumb[2]['link']['title']))
						$head_title = $breadcrumb[1]['link']['title'] .' - Mavic';
					else 
						$head_title = $breadcrumb[1]['link']['title'] .' - '. $breadcrumb[2]['link']['title'].' - Mavic';
				break;
				case 'technoline' :
				case 'prodvalcarac' :
					$head_title = $vars['node']->title.' - '.$breadcrumb[1]['link']['title'] .' - '.t('technology').' - Mavic';
				break;
				case 'history' :
					$head_title = $vars['node']->title.' - '.$breadcrumb[1]['link']['title'] .' - '.t('history').' - Mavic';
				break;
				case 'news' :
					$head_title = $vars['node']->title.' - '.$breadcrumb[1]['link']['title'] .' - '.t('news').' - Mavic';
				break;
				case 'download_category' :
					$head_title = $vars['node']->title.' - '.$breadcrumb[1]['link']['title'] .' - '.t('download').' - Mavic';
				break;
				case 'page' :
					$head_title = $vars['node']->title.' - Mavic';
				break;
				default : 
					$head_title = 'Mavic';
			}
			
		} else { // view all news
			$head_title = t('all news').' - Mavic';
			$keywords = '';
			$description = '';
		}
		
	}
	
}

//
// set breadcrumb
//
function clean_menu($menus) {
	$breadcrumb = '';
	foreach($menus as $key => $value) {
		if($value['link']['in_active_trail']) {
			$breadcrumb = array();
			$breadcrumb[] = array('link'=>array('title' => t('home page'), 'href' => '<front>', 'localized_options' => array(), 'type' => 0));
			set_mavic_breadcrumb($breadcrumb, $value, $key);
		}
	}
	return $breadcrumb;
}

//
// create breadcrumb (recursion)
//
function set_mavic_breadcrumb(&$breadcrumb, $value, $key) {
	$value['key_breadcrumb'] = $key;
	$breadcrumb[] = $value;
	if($value['link']['has_children']) {
		foreach($value['below'] as $key2 => $child) {
			if($child['link']['in_active_trail']) {
				set_mavic_breadcrumb($breadcrumb, $child, $key2);
				break;
			}
		}
	}
}
