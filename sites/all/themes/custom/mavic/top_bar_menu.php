<?php
	//
	// Top Bar Domain Link
	//
$roadactive = $mtbactive = $triactive = $comactive = "";
switch ($discipline){
	case 'road':
		$roadactive = " current";
		break;
	case 'mtb':
		$mtbactive = " current";
		break;
	default:
		if ($mPath == "shopfinder" || $mPath == "shopinshop" || $mPath =="distributor"){
			$triactive = " current";
		} else {
			$comactive = " current";
		}
}
	$top_menu = '<div class="top_menu_item first'.$comactive.'"><a href="http://'.$supdomain.'/'.$lang.'" class="link">mavic.com</a></div>
				<div class="top_menu_item'.$roadactive.'"><a href="http://roadcycling.'.$supdomain.'/'.$lang.'" class="link">'.t("road").'</a></div>
				<div class="top_menu_item'.$mtbactive.'"><a href="http://mtb.'.$supdomain.'/'.$lang.'" class="link">'.t("mountain bike").'</a></div>
				<div class="top_menu_item'.$triactive.'"><a href="http://'.$supdomain.'/'.$lang.'/shopfinder" class="link">'.t("find a dealer ").'</a></div>
				<div class="top_menu_item last"></div><div class="clear"></div>';		

?>
<div id="top_menu">
	<div id="tm_white_bg"></div>
	<div id="top_black_screen" ></div>
	<div id="top_menu_img">
		<img id="tm_img" src="<?php echo $landscape ?>" />
	</div>
	<div id="tm_items">
	<?php
	print $top_menu;
	?>
	</div>
</div>
