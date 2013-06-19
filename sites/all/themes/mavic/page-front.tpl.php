<?php 

	global $base_root;
	//
	// sub-domain
	//
	switch ($base_root) {
		case "http://roadcycling.".$supdomain:
			drupal_goto('http://roadcycling.'.$supdomain.'/'.$lang.'/road-home', NULL, NULL, 301);
			break;
		case "http://mtb.".$supdomain:
			drupal_goto('http://mtb.'.$supdomain.'/'.$lang.'/mtb-home', NULL, NULL, 301);
			break;
		case "http://triathlon.".$supdomain:
			//drupal_goto('http://triathlon.'.$supdomain.'/'.$lang.'/triathlon-home', NULL, NULL, 301);
			$mainMenu = 'menu_disc.php';
			break;
		default:
			$mainMenu = 'menu.php';
	}
?>
<?php require('header.php'); ?>
<script  type="text/javascript">
	$(window).bind("resize", function(){

		checkSize();
		//handle homeslide on homepage
		homeCheckSize();

		repositionDescription();
	});

	var initHome = function(){
		if (isTierIphone || isTierTablet) {
			var fixedSlider = $('#fixed_slider').detach();
			fixedSlider.children().each(function(i){
				if(i == 0){ 
					var imgSrc = $(this).find('.img').attr('src');
					$('<div id="body-background"><img src="' + imgSrc + '?v=1" width="1354" height="1200" alt="Bg"></div>').insertAfter('.home_footer');
				}
			});
			$(".top_menu_item last").add('.homebutton').remove();
			
			$("#body-background").ezBgResize();
			checkSize();
			$("#footer").add('#home_logo').css({"position":"static", "z-index": "1000"});
		} else { 
			$('HTML').css('overflow','auto');
			$("#homeslide_"+slides[activeSlide]).ezBgResize();
			checkSize();
			//handle homeslide on homepage
			homeCheckSize();
			homeShowSlide(0);
			homeTimer = setTimeout("homeAutoDefil()",6000);		
		};
	};

	var slides = new Array();
	var activeSlide = false;
	var homeTimer;
	var firstSlide = true;
	var oldSlide;

	var onHomePage = true;


</script>
	<div id="fixed_slider">
		<?php 
	if ($mobile != 'smartphone') {
		print $content; 
	} ?>
	</div>
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
		$comactive = " current";
}
	$top_menu = '<div class="top_menu_item first'.$comactive.'"><a href="http://'.$supdomain.'/'.$lang.'" class="link">mavic.com</a></div>
				<div class="top_menu_item'.$roadactive.'"><a href="http://roadcycling.'.$supdomain.'/'.$lang.'" class="link">'.t("road").'</a></div>
				<div class="top_menu_item'.$mtbactive.'"><a href="http://mtb.'.$supdomain.'/'.$lang.'" class="link">'.t("mountain bike").'</a></div>
				<div class="top_menu_item"><a href="http://'.$supdomain.'/'.$lang.'/shopfinder" class="link">'.t("find a dealer ").'</a></div>';
	if ($mobile != 'smartphone'){
	$top_menu .= '<div class="top_menu_item last"></div>';
	}
	$top_menu .= '<div class="clear"></div>';		
	?>
	<div id="top_menu" class="front">
		<div id="tm_items" class="front">
		<?php
		print $top_menu;
		?>
		</div>
	</div>
	<div id="container" class="home"  >
		<div id="black_screen"></div>
		<div id="subcontainer" class="home">
			<?php include($mainMenu) ?>
<?php 
			$nid = substr(drupal_get_normal_path('front_generic'),5);
			$content_encart = node_load($nid);
			if(!empty($content_encart->field_encart[0]['nid'])) {
?>
				<div id="home_right_content_container"><div id="home_right_content">
					<?php 
						foreach($content_encart->field_encart as $encart) 
							if(!empty($encart['nid']))
								echo node_view(node_load($encart['nid'])); 
					?>
				</div></div>
<?php 
			}
?>
		</div>
	</div>
	<div id="home_logo_container">
		<?php $attrTitle = $breadcrumb[0]['link']['title'];
		echo l($attrTitle,$breadcrumb[0]['link']['href'], array('attributes' => array('id' => 'home_logo', 'title' => $attrTitle))); ?>
	</div>
	<?php //TODO : surement un moyen de savoir le nombre total de slide sans passer par une requête...
		$query = "SELECT * FROM `node` as node WHERE (node.type='front') AND (node.language = '$lang') AND (node.status = 1)";
        $res = db_query($query);
		$maxHightlight = mysqli_num_rows($res);
		if($mobile != 'smartphone'){
		for ($i=0; $i<$maxHightlight;$i++){
		?>
		<div
		onclick="clearTimeout(homeTimer);homeShowSlide(<?php echo $i; ?>); "
		id="homebutton_<?php echo $i;?>"
		class="homebutton"
		style="right:<?php echo (($maxHightlight-1-$i)*13)+15; ?>px; "
		>
		</div>
	<?php }}?>
	<div id="forScrollTop" class="clear"></div>
<?php if(isset($_GET[nlfirstvisit])) : ?>
	<div id="nlfirstvisit">
		<a href="/<?php print $language->language ?>/newsletter/">
			<p  class="nlfirstvisit"><span class="helvetica"><?php print t('sign up') ?></span><br /><span class="helvetica"><?php print t('for Mavic') ?></span><br /><span class="helvetica"><?php print t('updates') ?></span></p>
		</a>
	</div>
<?php endif ?>
	<div id="footer" class="home_footer" >
		<?php print $footer;?>
	<div class="clear-both"><!-- --></div>
	</div>
	<?php if ($mobile == 'smartphone'): ?><div id="body-background"><img src="<?php echo $landscape; ?>?v=1" width="1354" height="900" alt="Bg"></div><?php endif; ?>
<?php require("footer.php"); ?>

<script  type="text/javascript">
<!--
initHome();
//-->
</script>

