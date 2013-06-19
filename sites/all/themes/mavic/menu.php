<?php 
function getLevel1ItemHtml($title, $titleId, $class, $href)
{
	return  '<a class="level1 '.$class.'" href="'.url($href).'" id="menu_'.$titleId.'">'.$title.'</a>'."\n".
			'<div class="menu_separator"></div>'."\n";
}
function getLevel1ItemHtmlClick($title, $href, $class)
{
	return  '<a class="level1 '.$class.'" href="'.url($href).'">'.$title.'</a>'."\n".'<div class="menu_separator"></div>'."\n";
}

function getLevel2ItemHtml($titleId, $level2Items, $i)
{
	$step = 33;
	// $decalage = 15;
	$decalage = 0;
	
	//le top est retouche en jquery dans script.js, au onload via jquery
	return
	
	'<div id="submenu_'.$titleId.'" class="submenu" style="top:'.($i * $step + $decalage).'px">
		'.getLevel2Html($level2Items).'
	</div>';
}

function getLevel2Html($level2Items)
{
	$total = count($level2Items);
	$i = 1;
	$out = 
	'<div class="submenucontent">';
			foreach ($level2Items as $item) :
		
				$classW = explode('_',$item->localized_options['attributes']['title']);
				
				if(($i+3)%4==0) //
				$out.= '<div class="row '.$classW[0].'">';
				if(!(($i%4==0)&&($classW[0] == 'wheels')))
				{
					$out.= '
					<a class="link '.$classW[0].'" href="'.url($item->href, $item->localized_options).'" >
						<img src="'.base_path().path_to_theme().$item->img_src.'" alt="" />
						<span class="helvetica" >'.$item->title.'</span>
					</a>';
				}
				if($i%4==0||$i==$total)
				{
					$out.= '<div class="clear"></div></div>';
				}
				$i++;
			endforeach;
	$out.= 
	'</div>';
	return $out;
}

function getSecondaryLinksLevel2($titleId, $level2Items, $i, $menu_news, $menu_video, $lang, $technologies_path, $menu_technologies, $node, $breadcrumb, $technologies_path_sys)
{
	$menu = @reset($menu_video);
	$step = 33;
	$out = '<div id="submenu_'.$titleId.'" class="submenu submenu_'.$titleId.' submenu_mavic" style="top:'.($i*$step+15).'px">
				<div class="submenucontent2">				
					<div class="right video">
						<div class="buttons">
						<div>
							<div class="right_buttons">			  
								<a class="prev browse left disabled"><img src="' . base_path().path_to_theme() . '/images/carousel_prev.gif" alt="" /></a>
								<a class="next browse right"><img src="' . base_path().path_to_theme() . '/images/carousel_next.gif" alt="" /></a>				
							</div>
							
								<p class="helvetica title">' . t('videos') . '</p></div>
								<div class="left_buttons">
									<img src="'. base_path().path_to_theme() . '/images/more_info.gif" alt=""><a href="' . url($menu['link']['href']) . '" >' . t('All videos') . '</a>
								</div>
							
							<div class="clear"></div>
						</div>
					  <!-- root element for scrollable -->
						<div class="scrollable view-content">
							<div class="items">'
					;
		$z = 0;
	foreach($menu_video as $video) {
		$z++;
		if ($z <= 4){
			$item = menu_get_item($video['link']['href']);
			$itemMap = $item['map'][1];
			$link = url($video['link']['href']);
			$imgpath = $itemMap->field_video_image[0]['filepath'];
			$imgpathsub = substr_replace($imgpath, 'menu', -8, -4);
			if(!file_exists($imgpathsub)){
				$imgpathsub = $itemMap->field_video_img_menu[0]['filepath'];
				if(!file_exists($imgpathsub) || empty($itemMap->field_video_img_menu[0]['filepath'])){
					$imgpathsub = 'sites/default/files/no_image_menu.jpg';
				}
			}
			$imgpathsub = base_path().$imgpathsub;
			if($video['link']['in_active_trail'])
			{
				$link = '#';
				$classe = ' isactive'; 
			}
			else $classe = '';
			$out.=		'	
					<div class="underitem">
						<div id="blockvideo" class="element'.$classe.'">
							<img width="236" alt="'. $itemMap->title .'" class="big" src="'. $imgpathsub . '">
							<a href="'. $link .'" class="link" style="display:none">'. $link .'</a>
						</div>
						<div class="clear mavic_menu_spacer"></div>
						<div class="complement">
									<p class="title"><a href="'. $link .'">'. $itemMap->title .'</a></p>
									<p class="description"><a href="'. $link .'">'	. $itemMap->field_page_description[0]['value'] . '</a></p>
						</div>
					</div>
		';	
		}
	}
	$menu = @reset($menu_technologies);
	if(!empty($menu['below']))
	{
		$menu = reset($menu['below']);
	}
	$out .=			'
							</div>
						</div>
					</div>
					<div class="right">
						'.views_embed_view("news","block_1").'

					</div>
					<div class="right techno">
						<div class="buttons">
						<div>
							<div class="right_buttons">			  
								<a class="prev browse left disabled"><img src="' . base_path().path_to_theme() . '/images/carousel_prev.gif" alt="" /></a>
								<a class="next browse right"><img src="' . base_path().path_to_theme() . '/images/carousel_next.gif" alt="" /></a>				
							</div>
							
								<p class="helvetica title">' . t('technologies') . '</p></div>
								<div class="left_buttons">
									<img src="'. base_path().path_to_theme() . '/images/more_info.gif" alt=""><a href="' . url($menu['link']['href']) . '" >' . t('All technologies') . '</a>
								</div>
							
							<div class="clear"></div>
						</div>
					  <!-- root element for scrollable -->
						<div class="scrollable view-content">
							<div class="items">'
					;
				$technocat = array(0 => "305520",1 => "305475",2 => "306355",3 => "305603");
				shuffle($technocat);
				foreach($technocat as $technocatlang){
						$technocattrad = translation_node_get_translations($technocatlang);
						$link = "node/" . $technocattrad[$lang]->nid;
						$item = menu_get_item($link);
						$itemMap = $item['map'][1];
                                                
						if($node->nid == $technocattrad[$lang]->nid)
						{
							$link = '#';
							$classe = ' isactive'; 
							$textcontent = $node->content['body']['#value'];
						
						}
						else 
						{
							$link = url($link);
							$classe = '';
							$textcontent = $itemMap->body;
						}
                                                foreach($itemMap->field_feature_codes as $ChildFeatureCodeValue){
                                                    $childFeatureCodeFound = '';
                                                    if( file_exists( $technologies_path_sys.'/brother/'.$ChildFeatureCodeValue["value"].'.jpg')){
                                                            $childFeatureCodeFound = $ChildFeatureCodeValue['value'];
                                                            break;
                                                    } 
                                                }
                                                
						$out.=		'	
								<div class="underitem">
									<div id="blocktechno" class="element'.$classe.'">
										<img width="236" height="133" alt="'. $itemMap->title .'" class="big" src="'. $technologies_path.'/brother/'. $childFeatureCodeFound .'.jpg">
										<a href="'. $link .'" class="link" style="display:none">'. $link .'</a>
									</div>
									<div class="clear mavic_menu_spacer"></div>
									<div class="complement">
												<p class="title"><a href="'. $link .'">'. $itemMap->title .'</a></p>
												<p class="description"><a href="'. $link .'">'. $textcontent .'</a></p>
									</div>
								</div>
					';	

				}
	$out .=			'
							</div>
						</div>
					</div>
					<div class="left">';
					
	foreach($level2Items as $item)
	{
		$out.= '<div class="level2">';
		
		$out.= '<span class="helvetica">'.$item->title.'</span>';
		
		if (isset($item->below)) {
			$out.='<div class="wraplevel3" >';
			foreach($item->below as $belowItem)
			{
				$out .='<a class="level3" href="'.url($belowItem['link']['href']).'">'.$belowItem['link']['title'].'</a>';
			}
			$out.='</div>';
			$out .= '<div class="level3_separator"></div>';
		}
		$out .= '</div>';
	}
	$out.= '		</div>
				</div>
			</div>';

	return $out;
}


	$i=0;
	$output  = $output2 = '';

	
	//
	// product link
	//
	$system_path = 'node/'.arg(1);
	$current_path = drupal_get_path_alias($system_path);
	
	foreach ($primary_links as $data) {
		$class = 'helvetica';
		if($data['link']['in_active_trail'])
			$class.= ' current';
		$level1ItemTitleId = $data['link']['mlid'];
		$level2Items = array();
		if(count($data['below']) < 2) {
			$subdata = @reset($data['below']);
			$subsubElem = @reset($subdata['below']);
			$output.= getLevel1ItemHtmlClick($data['link']['title'], $subsubElem['link']['href'], $class);
		} else {
			$output.= getLevel1ItemHtml($data['link']['title'], $level1ItemTitleId, $class, $data['link']['href']);
			if ($current_path != 'product/'.$data['link']['title']) {
				foreach ($data['below'] as $j => $subdata)
				{
					$subsubElem = @reset($subdata['below']);
					$subsubNext = @next($subdata['below']);
					$level2Item = new stdClass();
					$level2Item->title = $subdata['link']['title'];
					$level2Item->img_src = '/images/menus/'.$subdata['link']['options']['attributes']['title'].".jpg";
					$level2Item->href = $subsubElem['link']['href'];
					$level2Item->localized_options = $subdata['link']['localized_options'];
					$level2Item->cat_title = $subsubNext['link']['title'];
					$level2Item->cat_href = $subsubNext['link']['href'];
					$level2Items[] = $level2Item;
				}
				$output.= getLevel2ItemHtml($level1ItemTitleId, $level2Items, $i);
			}
		}
		$i++;
	}
	
	//
	// MAVIC link
	//
	$class = 'helvetica';
	switch($active_menu_name) {
		case 'menu-photo' :
		case 'menu-menu-technologies-'.$lang :
		case 'menu-videos' :
		case 'menu_athletes' :
		case 'menu-sport' :
		case 'menu-news' :
		case 'menu-history' :
		case 'menu-assistance-'.$lang :
		case 'menu-careers' :
			$class.= ' current';
	}
	if ($current_path == 'mavic'){
		$class.= ' current';
	}
	$level1ItemTitleId = 'MAVICItem';
	$level2Items = array();
	$output.= getLevel1ItemHtml(t('Mavic'), $level1ItemTitleId, $class, 'mavic');
/*	
	if($menu = @reset($menu_photo)) { // menu photo ----------------------
		$level2Item = new stdClass();
		$level2Item->title = t('photogallery');
		$m = @reset($menu['below']);
		if(!empty($m['link']['href'])) {
			$level2Item->href = $m['link']['href'];
			$level2Items[] = $level2Item;
		}
	}
	*/
	$level2Item = new stdClass();// menu sport ----------------------
	$level2Item->title = t('sport');
	$level2Item->below = array();	  
	if($menu = @reset($menu_athlete)) { // menu athlete
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('athlete');
		$level2Item1['link']['href'] = $menu['link']['href'];
		$level2Item->below[] = $level2Item1;
	}
	if($menu = @reset($menu_assistance)) { 
		$level2Item1 = array('link'=>array());// menu neutral support  ----------------------
		if(!empty($menu['link']['href'])) {
			$level2Item1['link']['title'] = $menu['link']['title'];
			$level2Item1['link']['href'] = $menu['link']['href'];
			$level2Item->below[] = $level2Item1;
		}
		$level2Item1 = array('link'=>array());// menu events  ----------------------
		$menu = next($menu_assistance);
		if(!empty($menu['link']['href'])) {
			$level2Item1['link']['title'] = $menu['link']['title'];
			$level2Item1['link']['href'] = $menu['link']['href'];
			$level2Item->below[] = $level2Item1;
		}
    }

	$level2Items[] = $level2Item;
	$level2Item = new stdClass(); // menu where to buy ----------------------
	$level2Item->title = t('where to buy');
	$level2Item->below = array();
	$level2Item1 = array('link'=>array()); // menu shopfinder ----------------------
	$level2Item1['link']['title'] = t('find a dealer');
		$level2Item1['link']['href'] = 'shopfinder';
        $level2Item->below[] = $level2Item1;
	$level2Item1 = array('link'=>array()); // menu shopinshop ----------------------
	$level2Item1['link']['title'] = t('shop in shops');
		$level2Item1['link']['href'] = 'shopinshop';
        $level2Item->below[] = $level2Item1;
	$level2Item1 = array('link'=>array()); // menu distributor ----------------------
	$level2Item1['link']['title'] = t('find a distributor');
		$level2Item1['link']['href'] = 'distributor';
        $level2Item->below[] = $level2Item1;
	$level2Items[] = $level2Item;
	
	$level2Item = new stdClass(); // menu company ----------------------
	$level2Item->title = t('company');
	$level2Item->below = array();
	$level2Item1 = array('link'=>array()); // menu who we are ----------------------
	$level2Item1['link']['title'] = t('who we are?');
		$level2Item1['link']['href'] = str_replace(array('&','?','='),'',$level2Item1['link']['title']);
        $level2Item->below[] = $level2Item1;
	if($menu = @reset($menu_history)) { // menu history ----------------------
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('history');
		$m = reset($menu['below']);
		$level2Item1['link']['href'] = $m['link']['href'];
		$level2Item->below[] = $level2Item1;
    }
	if ($menu = @reset ($menu_careers)) {// menu careers ----------------------
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('careers');
		$level2Item1['link']['href'] = $menu['link']['href'];
		$level2Item->below[] = $level2Item1;
	}
	$level2Item1 = array('link'=>array()); // menu service ----------------------
	$level2Item1['link']['title'] = t('Mavic Service Center');
        $level2Item1['link']['href'] = str_replace(array('&','?','='),'',$level2Item1['link']['title']);
        $level2Item->below[] = $level2Item1;
	$level2Items[] = $level2Item;
	

	if ($current_path != 'mavic'){
		$output2.= getSecondaryLinksLevel2($level1ItemTitleId,$level2Items, $i, $menu_news, $menu_video, $lang, $technologies_path, $menu_technologies, $node, $breadcrumb, $technologies_path_sys);
	}
?>
<div id="menu" >
    <div class="menu_main">
	<?php
	print $output;
	?>
	<?php
	print $output2;
	 ?>
		<form id="search_form" action="/<?php echo $lang?>/search/google_cse_adv" method="post">
			<script type="text/javascript" src="http://www.google.com/jsapi"></script>
			<script type="text/javascript">
				google.load('search', '1');
				google.setOnLoadCallback(function() {
					google.search.CustomSearchControl.attachAutoCompletion(
						'014338948727438158850:bsnpnwdxyvo',
						document.getElementById('search_input'),
						'search_form');
				});
			</script>
			<div id="search_wrapper">
				<h1 id="search_hint" class="helvetica"><?php echo t('search...');?></h1>
				<input class="" id="search_input" type="text" name="keys" value="" maxlength="30" autocomplete="off" />
			</div>
			<a class="submit" href="javascript: document.getElementById('search_form').submit();"><?php echo t('OK')?></a>
			<input type="hidden" value="<?php print drupal_get_token('search_form'); ?>" name="form_token" />
			<input type="hidden" value="search_form" id="edit-search-form" name="form_id" />
			
			<div class="clear" style=""></div>
		</form>
    </div>
</div>






