<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
 global $language;
 $absolute_theme_path=base_path().path_to_theme().'/';
 $primary_links_array = array_values($primary_links);
 $range_links = $primary_links_array[count($primary_links_array)-1]['below'];
 $onmi[] ='';
 $onmi[] ='shorts';
 $onmi[] ='outwear';
 $onmi[] ='jerseys';
 $onmi[] ='underwear';
 $onmi[] ='gloves';
 $onmi[] ='warmers';
 $onmi[] ='socks';
 
 ?><html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language ?>" xml:lang="<?php print $language->language ?>" dir="ltr">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php print $title ?></title>
<script type="text/javascript" src="<?php print $absolute_theme_path ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php print $absolute_theme_path ?>js/landingpage/jquery-easing.1.2.pack.js"></script>
<script type="text/javascript" src="<?php print $absolute_theme_path ?>js/s_code.js"></script>
<script type="text/javascript" src="<?php print $absolute_theme_path ?>js/landingpage/masterpage.js"></script>
<script type='text/javascript' src='<?php print $absolute_theme_path ?>js/landingpage/cufon-yui.js'></script>
<script type='text/javascript' src='<?php print $absolute_theme_path ?>js/landingpage/Helvetica75_700.font.js'></script>
<script type="text/javascript" src="<?php print $absolute_theme_path ?>js/jquery.autoellipsis-1.0.2.min.js"></script>

<script type='text/javascript'> var language = '<?php print $lang ?>';</script>
<link type="text/css" rel="stylesheet" media="all" href="<?php print $absolute_theme_path ?>style-landingpage.css" />
</head>
<body>
<div id="site">
<div id="backlink"><a href="/<?php print $language->language ?>">www.mavic.com</a></div>
<div id="screens">
<div class="nav nav-left"><div id="btn-left"><!-- --></div></div>
<div class="nav nav-right"><div id="btn-right"><!-- --></div></div>
<div id="inner-screens">
	<div class="screen" id="screen2"><?php
$nod = node_load($node->field_landing_macromodel[0]['nid']);
$nameprod = $nod->title;
$hrefprod = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_cyclone_jacket';
$descprod = $nod->field_page_description[0]['value'];
$nod = node_load($node->field_landing_macromodel[1]['nid']);
$nameprod2 = $nod->title;
$hrefprod2 = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_stratos_short';
$descprod2 = $nod->field_page_description[0]['value'];
 ?>
	<div class="background-img"><div class="inner"><img src="<?php print $absolute_theme_path ?>images/landingpage/screen1.jpg" width="1200" height="1200" border="0" alt="<?php print $nameprod ?> - <?php print $nameprod2 ?>" /></div></div>
	<div class="content right">
	<div class="slide-title"><img src="<?php print $absolute_theme_path ?>images/landingpage/titre1_<?php print $language->language ?>.png" border="0" alt="<?php print $title ?>" /></div>
	<div class="range-btn"><a href="#"><img src="<?php print $absolute_theme_path ?>images/landingpage/discover_<?php print $language->language ?>.png" border="0" alt="Discover the range" /></a></div>
	<div class="product-block">
	<div class="product">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/cyclone_jacket.jpg" width="132" height="98" border="0" alt="<?php print $nameprod ?>" />
	<h2><?php print $nameprod ?></h2>
	<p><?php print $descprod ?></p>
	<a href="<?php print $hrefprod ?>" class="more"><?php print t('more') ?></a>
	</div>
	<div class="product">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/cyclone_bib.jpg" width="132" height="98" border="0" alt="<?php print $nameprod2 ?>" />
	<h2><?php print $nameprod2 ?></h2>
	<p><?php print $descprod2 ?></p>
	<a href="<?php print $hrefprod2 ?>" class="more"><?php print t('more') ?></a>
	</div>
	</div>
	</div>
	</div>
	<div class="screen" id="screen3"><?php
$nod = node_load($node->field_technologienode[0]['nid']);
$nameprod4 = $nod->title;

$nod = node_load($node->field_technologienode[1]['nid']);
$nameprod = $nod->title;
$nod = node_load($node->field_technologienode[2]['nid']);
$hrefprod = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_inserts';
$descprod = $nod->body;

$nod = node_load($node->field_technologienode[3]['nid']);
$nameprod2 = $nod->title;
$hrefprod2 = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_ergozip';
$descprod2 = $nod->body;

$nod = node_load($node->field_technologienode[4]['nid']);
$nameprod3 = $nod->title;
$hrefprod3 = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_ergocuff';
$descprod3 = $nod->body;
 ?>
	<div class="background-img"><div class="inner"><img src="<?php print $absolute_theme_path ?>images/landingpage/screen3.jpg" width="1200" height="1200" border="0" alt="<?php print $title ?>" /></div></div>
	<div class="content left">
	<div class="slide-title"><img src="<?php print $absolute_theme_path ?>images/landingpage/titre2_<?php print $language->language ?>.png" border="0" alt="<?php print $nameprod4; ?>" /></div>
	<div class="range-btn"><a href="#"><img src="<?php print $absolute_theme_path ?>images/landingpage/discover_<?php print $language->language ?>.png" border="0" alt="Discover the range" /></a></div>
	<div class="product-block">
	<div class="product">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/insert_ergo_short.jpg" width="132" height="98" border="0" alt="<?php print $nameprod ?>" />
	<h2><?php print $nameprod ?></h2>
	<p class="techno_desc"><?php print $descprod ?></p>
	<a href="<?php print $hrefprod ?>" class="more"><?php print t('more') ?></a>
	</div>
	<div class="product">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/ergo_zip.jpg" width="132" height="98" border="0" alt="<?php print $nameprod2 ?>" />
	<h2><?php print $nameprod2 ?></h2>
	<p class="techno_desc"><?php print $descprod2 ?></p>
	<a href="<?php print $hrefprod2 ?>" class="more"><?php print t('more') ?></a>
	</div>
	<div class="product">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/ergo_cuff.jpg" width="132" height="98" border="0" alt="<?php print $nameprod3 ?>" />
	<h2><?php print $nameprod3 ?></h2>
	<p class="techno_desc"><?php print $descprod3 ?></p>
	<a href="<?php print $hrefprod3 ?>" class="more"><?php print t('more') ?></a>
	</div>
	</div>
	</div>
	</div>
	<div class="screen" id="screen4"><?php
$nod = node_load($node->field_technologienode[5]['nid']); 
$nameprod = $nod->title;
$hrefprod = '/'.$language->language.'/'.$nod->path.'?intcmp=landingpageFW11_climaride';
?>
	<div class="background-img"><div class="inner"><img src="<?php print $absolute_theme_path ?>images/landingpage/screen2.jpg" width="1200" height="1200" border="0" alt="<?php print $title ?>" /></div></div>
	<div class="content right">
	<div class="slide-title"><img src="<?php print $absolute_theme_path ?>images/landingpage/titre3_<?php print $language->language ?>.png" border="0" alt="<?php print $nameprod; ?>" /></div>
	<div class="range-btn"><a href="#"><img src="<?php print $absolute_theme_path ?>images/landingpage/discover_<?php print $language->language ?>.png" border="0" alt="Discover the range" /></a></div>
	<div class="product-block">
	<div id="pictos">
	<div class="picto">
	<a href="<?php print $hrefprod; ?>"><img src="<?php print $absolute_theme_path ?>images/landingpage/windride.gif" width="132" height="98" border="0" alt="Wind Ride" /></a>
	</div>
	<div class="picto">
	<a href="<?php print $hrefprod; ?>"><img src="<?php print $absolute_theme_path ?>images/landingpage/rainride.gif" width="132" height="98" border="0" alt="Rain Ride" /></a>
	</div>
	<div class="picto">
	<a href="<?php print $hrefprod; ?>"><img src="<?php print $absolute_theme_path ?>images/landingpage/rainrideplus.gif" width="132" height="98" border="0" alt="Rain Ride+" /></a>
	</div>
	<div class="picto">
	<a href="<?php print $hrefprod; ?>"><img src="<?php print $absolute_theme_path ?>images/landingpage/hotride.gif" width="132" height="98" border="0" alt="Cold Ride" /></a>
	</div>
	<div class="picto">
	<a href="<?php print $hrefprod; ?>"><img src="<?php print $absolute_theme_path ?>images/landingpage/hotrideplus.gif" width="132" height="98" border="0" alt="Cold Ride+" /></a>
	</div>
	</div>
	</div>
	</div>
	</div>
	<div class="screen" id="screen5">
	<div class="content">
	<div><img src="<?php print $absolute_theme_path ?>images/landingpage/discover_big_<?php print $language->language ?>.png" border="0" alt="discover the range" /></div>
	<div class="product-block">
	<div id="products"><?php
	$i=0;
	foreach($range_links as $itemlvl1){
		foreach($itemlvl1['below'] as $item){
		$i++; ?>
	<div class="prodrange">
	<img src="<?php print $absolute_theme_path ?>images/landingpage/img<?php print $i ?>.jpg" width="221" height="154" border="0" alt="<?php print $itemlvl1['link']['title'] ?>" />
	<div><a href="<?php print url($item['link']['href'], $item['link']['localized_options']).'?intcmp=landingpageFW11_'.$onmi[$i] ?>"><?php
		$tmp = explode('/',$itemlvl1['link']['title']);
		if(count($tmp)==1){
			print $tmp[0];
		}
		else{
			foreach($tmp as $key =>$value){
				$tmp[$key]= trim($value);
			}
			print '<span class="first">'.implode('</span><br /><span>',$tmp).'</span>';
		}
	?></a></div>
	</div>
	<?php
			break;
		}
	} ?>
	</div>
	</div>
	</div>
	</div>
</div>
<div id="logo"><a href="/"><img src="<?php print $absolute_theme_path ?>images/landingpage/logo.gif" width="167" height="101" border="0" alt="Mavic" /></a></div>
</div>
</div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//--><!]]>
</script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
_init();

var _gaq = _gaq || [];
var pageTracker = _gat._getTracker('UA-2489222-1');
pageTracker._setDomainName("mavic.com");
pageTracker._trackPageview('/<?php print $language->language ?>/landingpage/apparelFW11');
 
s.pageName="landingpage:apparelFW11";
s.channel="landingpage";
s.prop1="";
s.prop2="";
s.prop3="";
s.prop4="";
s.prop12="<?php print $language->language ?>";
s.prop31="mavic.com";
		/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
var s_code=s.t();if(s_code)document.write(s_code)
//--><!]]>
</script>
</body>
</html>