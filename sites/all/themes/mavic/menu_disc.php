<?php 
	$a_translat = array();//rims - wheels
	$art_translat = array();//footwear
	$an_translat = array();//news
	$at_translat = array();//athletes
	$roadtria_translat = array(
		"en" => "road-triathlon",
		"fr" => "route-et-triathlon",
		"de" => "rennrad-triathlon",
		"es" => "carretera-y-triatlón",
		"it" => "strada-triathlon",
		"ja" => "ロード＆トライアスロン"
	);
	$road_translat = array(
		"en" => "road",
		"fr" => "route",
		"de" => "rennrad",
		"es" => "carretera",
		"it" => "strada",
		"ja" => "ロード"
	);
	$tria_translat = array(
		"en" => "triathlon",
		"fr" => "triathlon",
		"de" => "triathlon",
		"es" => "triatlón",
		"it" => "triathlon",
		"ja" => "トライアスロン"
	);
	$mtb_translat = array(
		"en" => "mountain-bike",
		"fr" => "VTT",
		"de" => "MTB",
		"es" => "MTB",
		"it" => "mountain-bike",
		"ja" => "MTB"
	);
	$mtb_athlete_translat = array(
		"en" => "mountain bike",
		"fr" => "VTT",
		"de" => "MTB",
		"es" => "MTB",
		"it" => "mountain bike",
		"ja" => "MTB"
	);
	$mtb_news_translat = array(
		"en" => "MTB",
		"fr" => "VTT",
		"de" => "MTB",
		"es" => "MTB",
		"it" => "MTB",
		"ja" => "MTB"
	);
switch ($discipline) {
	case 'road':
		$a_translat = $an_translat = $at_translat = $road_translat;
		$art_translat = $roadtria_translat;
		break;
	case 'triathlon':
		$a_translat = $an_translat = $at_translat = $tria_translat;
		$art_translat = $roadtria_translat;
		break;
	case 'mtb':
		$a_translat = $art_translat = $mtb_translat;
		$an_translat = $mtb_news_translat;
		$at_translat = $mtb_athlete_translat;
		break;
}
$system_path = 'node/'.arg(1);
$current_path = drupal_get_path_alias($system_path);
function getLevel1ItemHtml($title, $titleId, $class,$href)
{
	return  '<a class="level1 '.$class.'" href="'.url($href).'" id="menu_'.$titleId.'">'.$title.'</a>'."\n".
			'<div class="menu_separator"></div>'."\n";
}

function getLevel1ItemHtmlClick($title, $href, $class)
{
	return  '<a class="level1 '.$class.'" href="'.url($href).'">'.$title.'</a>'."\n".'<div class="menu_separator"></div>'."\n";
}

function getSecondaryLinksLevel2($titleId, $level2Items)
{
	$step = 33;
	$as = 0;
	$out = '<div id="submenu_'.$titleId.'" class="submenu submenu_'.$titleId.' submenu_mavic disc" >
				<div class="submenucontent2">				
					<div class="left">';
					
	foreach($level2Items as $item)
	{
		$as++;//do not sent to main domain if Sport menu
		$out.= '<div class="level2">';
		
		$out.= '<span class="helvetica">'.$item->title.'</span>';
		
		if (isset($item->below)) {
			$out.='<div class="wraplevel3" >';
			foreach($item->below as $belowItem)
			{
				$link = url($belowItem['link']['href'], array('absolute'=>'true','base_url'=>'http://www.mavic.com'));
				$out .='<a class="level3" href="'.$link.'">'.$belowItem['link']['title'].'</a>';
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
	// home link
	//
	$output.= '<a class="level1 helvetica first" href="'.url($breadcrumb[0]['link']['href']).'" id="menu_home">'. $an_translat[$lang] .'</a>'."\n";	
	//
	// product link
	//
	$class = 'helvetica';
	$level1ItemTitleId = 'productsItem';
	if(($active_menu_name == 'menu-primary-links-'.$lang) || ($current_path == 'products') ) $class.= ' current';
	$output.= getLevel1ItemHtml(t('products'), $level1ItemTitleId, $class, 'products');
	if ($current_path != 'products'){
		$level2Items = array();
		$output.='<div id="submenu_productsItem" class="submenu" style="top:33px">
					<div class="submenucontent">
					<ul class="disc_sub_item left">';
		foreach ($primary_links as $data) {
			$i++;
			$class2 = 'helvetica';
			if($data['link']['in_active_trail']) $class2.= ' current';
			$level2ItemTitleId = $data['link']['mlid'];
			$level2ItemTitle = $data['link']['title'];
			if ($i < 3) $output.='<li class ="'.$class2.'" id="menu_'.$level2ItemTitleId.'"><a href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$art_translat[$lang].'/'.$level2ItemTitle.'">'.$level2ItemTitle.'</a></li>';//wheels_1 - rims_2
			if ($i > 2 && $i < 5 && !($i==3 && $discipline == 'mtb' )) $output.='<li class ="'.$class2.'" id="menu_'.$level2ItemTitleId.'"><a href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$level2ItemTitle.'/'.$level2ItemTitle.'">'.$level2ItemTitle.'</a></li>';//tyres_3 - computers - pedals - helmets_4 (only computers and helmets for MTB)
			if ($i == 5) $output.='<li class ="'.$class2.'" id="menu_'.$level2ItemTitleId.'"><a href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$a_translat[$lang].'/'.$level2ItemTitle.'">'.$level2ItemTitle.'</a></li>';//footwear_5
			if ($i == 6) { //apparel_6
				$output.='</ul><ul class="disc_sub_item right"><li class ="'.$class2.'" id="menu_'.$level2ItemTitleId.'">'.$level2ItemTitle.'<br /><ul class="disc_sub_sub_item">';
				foreach ($data['below'] as $j => $subdata){
					$class3 = '';
					$subsubElem = @reset($subdata['below']);
					$level3ItemTitle = $subdata['link']['title'];
					$level3ItemHref = $subsubElem['link']['href'];
					if($subsubElem['link']['in_active_trail']) $class3 = 'current';
					$output.='<li class ="'.$class3.'"><a href="'.url($level3ItemHref).'">'.$level3ItemTitle.'</a></li>';
				}
				$output.='</ul></li>';
			}
		}	
		$output.='	</ul></div></div>';
	}
	//
	// News link
	//
	foreach($menu_news as $data)
	{
		if ($data['link']['title'] == $an_translat[$lang]){
			$classes = 'level1 helvetica';
			if ($data['link']['in_active_trail']) $classes.= " current";
			if($data['link']['expanded']) {
				$first = reset($data['below']);
				$link = $first['link']['href'];
			} else {
				$link = $data['link']['href'];
			}
			$output.= getLevel1ItemHtmlClick(t('news'), $link, $classes);
		}
	}
	//
	// video link
	//
	$menuV = @reset($menu_video);
	$classes = 'level1 helvetica';
	if ($menuV['link']['in_active_trail']) $classes.= " current";
	$output.= '<a class="level1 '.$classes.'" href="'.url($menuV['link']['href']).'">'.t('video').'</a>'."\n".'<div class="menu_separator"></div>'."\n";
	
	//
	// event link
	//
	$menuAs = @reset($menu_assistance);
	$classes = 'level1 helvetica';
	if ($menuAs['link']['in_active_trail']) $classes.= " current";
	$output.= getLevel1ItemHtmlClick(t('events'), $menuAs['link']['href'], $classes);
	//
	// athletes link
	//
	foreach($menu_athlete as $data)
	{
		if ($data['link']['title'] == $at_translat[$lang]){
			$classes = 'level1 helvetica';
			if ($data['link']['in_active_trail']) $classes.= " current";
			$link = $data['link']['href'];
			$output.= getLevel1ItemHtmlClick(t('athletes'), $link, $classes);
		}
	}
	//
	// techno link
	//
	$menuT = @reset($menu_technologies);
	foreach ($menuT['below'] as $techno) {
		$techItem = menu_get_item($techno['link']['href']);
		$techitemMap = $techItem['map'][1];
		$isDisc = false;
					$macro_assoc = db_query('SELECT n.nid FROM content_field_technologienode o INNER JOIN node n USING (nid,vid), menu_links m WHERE o.field_technologienode_nid='.$techitemMap->nid.' and m.link_path=concat("node/", o.nid) and n.status=1 order by m.weight');
					while ($assocP = db_fetch_array($macro_assoc)){
						$dischref = url('node/'.$assocP['nid']);
						if (strpos($dischref, $a_translat[$lang])) $isDisc = true;					
					}
		if ($isDisc) {
			$classes = 'level1 helvetica';
			if ($menuT['link']['in_active_trail']) $classes.= " current";
			$output.= getLevel1ItemHtmlClick(t('technologies'), $techno['link']['href'], $classes);
			break;
		}
	}
	//
	// MAVIC link
	//
	$level1ItemTitleId = 'MAVICItem';
	$classM = 'helvetica disc';
	if ($current_path == 'mavic') { 
		$classM .= ' current';
		$output.= getLevel1ItemHtml(t('Mavic'), $level1ItemTitleId, $classM, 'mavic');
	} else {
		$output.= getLevel1ItemHtml(t('Mavic'), $level1ItemTitleId, $classM, 'mavic');
		$level2Items = array();
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
	
		$output2.= getSecondaryLinksLevel2($level1ItemTitleId,$level2Items);
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
