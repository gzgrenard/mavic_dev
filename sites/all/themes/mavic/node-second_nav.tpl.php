<script type="text/javascript" >

$(document).ready(function() {
	$("#body-background").ezBgResize();
	checkSize();
	$('#main_content').css({'background-color':'transparent', 'padding':0});

});
</script>
<div id="tabs" class="midlevel">

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
$i = 0;
$level2Items = array();
$output='<div id="submenu_productsItem" class="submenu_page">';
foreach ($primary_links as $data) {
	$i++;
	$level2ItemTitleId = $data['link']['mlid'];
	$level2ItemTitle = $data['link']['title'];
	$productTnidSet = translation_node_get_translations(db_result(db_query('SELECT tnid FROM `node` WHERE `type` = "line" AND `title` = "'.$level2ItemTitle.'"')));
	$level2ItemTitleEn = $productTnidSet['en']->title;
	$level2ItemImg = '/images/menus/disc/'.$discipline.'_'.$level2ItemTitleEn.'.jpg';
	$objPropVar = 'field_nav_' . $discipline . '_' . $level2ItemTitleEn;
	if (isset($node->$objPropVar)) {
		$nodeObj = $node->$objPropVar;
	} else {
		$level2ItemDesc = '';
		$nodeObj = '';
	}
	!empty($nodeObj) ? $level2ItemDesc = $nodeObj[0]['value'] : $level2ItemDesc = '';
	if ($i < 3) $output.='<a class ="link" href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$art_translat[$lang].'/'.$level2ItemTitle.'"><img src="'.base_path().path_to_theme().$level2ItemImg.'" alt="" /><span class="helvetica navtitle" >'.$level2ItemTitle.'</span><p class="navdesc">'.$level2ItemDesc.'</p></a>';//wheels - rims
	if ($i > 2 && $i < 5 && !($i==3 && $discipline == 'mtb' )) $output.='<a class ="link" href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$level2ItemTitle.'/'.$level2ItemTitle.'"><img src="'.base_path().path_to_theme().$level2ItemImg.'" alt="" /><span class="helvetica navtitle" >'.$level2ItemTitle.'</span><p class="navdesc">'.$level2ItemDesc.'</p></a>';//tyres - computers - pedals - helmets (only computers and helmets for MTB)
	if ($i == 5) $output.= '<a class ="link" href="/'.$lang.'/product/'.$level2ItemTitle.'/'.$a_translat[$lang].'/'.$level2ItemTitle.'"><img src="'.base_path().path_to_theme().$level2ItemImg.'" alt="" /><span class="helvetica navtitle" >'.$level2ItemTitle.'</span><p class="navdesc">'.$level2ItemDesc.'</p></a>';//footwear
	
	if ($i == 6) {
		//separator
		$output .= '<div class="clear"></div></div>
					<div id="submenu_productsItem2" class="submenu_page">';
		//apparel
		$output.='<h2 class ="helvetica navsubtitle">'.$level2ItemTitle.'</h2>';
		foreach ($data['below'] as $j => $subdata){
			$subsubElem = @reset($subdata['below']);
			$level3ItemTitle = $subdata['link']['title'];
			$productTnidSet = translation_node_get_translations(db_result(db_query('SELECT tnid FROM `node` WHERE `type` = "category" AND `title` = "'.$level3ItemTitle.'"')));
			$level3ItemTitleEn = $productTnidSet['en']->title;
			$level3ItemHref = $subsubElem['link']['href'];
			$level3ItemImg = '/images/menus/disc/'.$discipline.'_'.$level3ItemTitleEn.'.jpg';
			$objPropVar = 'field_nav_' . $discipline . '_' . $level3ItemTitleEn;
			if (isset($node->$objPropVar)) {
				$nodeObj = $node->$objPropVar;
			} else {
				$level3ItemDesc = '';
				$nodeObj = '';
			}
			!empty($nodeObj) ? $level3ItemDesc = $nodeObj[0]['value'] : $level3ItemDesc = '';
			$output .= '<a class ="link" href="'.url($level3ItemHref).'"><img src="'.base_path().path_to_theme().$level3ItemImg.'" alt="" /><span class="helvetica navtitle" >'.$level3ItemTitle.'</span><p class="navdesc">'.$level3ItemDesc.'</p></a>';
		}
		$output.='<div class="clear"></div></div>';
	}
}
$output.='	</div>';
print $output;
?>
</div>