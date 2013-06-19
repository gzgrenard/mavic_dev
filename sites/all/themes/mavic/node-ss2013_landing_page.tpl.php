
<?php

function replace_special_char($nom) {
	return str_replace(array('/', ' ', 'à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'), array('_', '_', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'), $nom);
}

$omnitureDownload = 'landinpage_SSapparel13_download';
switch ($lang) {
	case 'fr':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'DECOUVRIR LES GAMMES';
		$couterTitlesRoad = array('Route premium homme','Route ultra-léger homme','Route performance homme','Route premium femme','Enduro homme','All mountain homme','All mountain ultra-léger homme');
		$couterTitlesMtb = array('Enduro homme','All mountain homme','All mountain ultra-léger homme','Route premium homme','Route ultra-léger homme','Route performance homme','Route premium femme');
		$technohref = '/fr/technology/textile/Clima-Ride/Clima-Ride';
		$ergoRideCat = array('casques', 'chaussures', 'casques', 'textile', 'chaussures', 'textile');
		$downloadTrad = 'télécharger le catalogue 2013';
		$helmetHref = '/fr/product/casques/casques/casques';
		break;
	case 'en':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'DISCOVER THE RANGES';
		$couterTitlesRoad = array('Men’s premium road', 'Men’s superlight road', 'Men’s road racing', 'Women’s premium road', 'Men’s Enduro', 'Men’s rugged Trail', 'Men’s lightweight trail');
		$couterTitlesMtb = array('Men’s Enduro', 'Men’s rugged Trail', 'Men’s lightweight trail', 'Men’s premium road', 'Men’s superlight road', 'Men’s road racing', 'Women’s premium road');
		$technohref = '/en/technology/apparel/Clima-Ride/Clima-Ride';
		$helmetHref = '/en/product/helmets/helmets/helmets';
		$ergoRideCat = array('helmets', 'footwear', 'helmets', 'apparel', 'footwear', 'apparel');
		$downloadTrad = 'download the 2013 catalog';
		break;
	case 'de':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'Entdecken Sie die Kollektion';
		$couterTitlesRoad = array('Men Rennrad Premium','Men Rennrad Superlight','Men Rennrad Racing','Women Rennrad Premium','Men Enduro','Men’s rugged Trail','Men’s lightweight trail');
		$couterTitlesMtb = array('Men Enduro','Men’s rugged Trail','Men’s lightweight trail', 'Men Rennrad Premium','Men Rennrad Superlight','Men Rennrad Racing','Women Rennrad Premium');
		$technohref = '/de/technology/bekleidung/Clima-Ride/Clima-Ride';
		$ergoRideCat = array('helme', 'schuhe', 'helme', 'bekleidung', 'schuhe', 'bekleidung');
		$downloadTrad = 'Katalog 2013 download';
		$helmetHref = '/de/product/helme/helme/helme';
		break;
	case 'it':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'SCOPRI LE GAMME';
		$couterTitlesRoad = array('Uomo strada premium ','Uomo strada superlight ','Uomo corsa strada ','Donna strada premium ','Uomo Enduro ','Uomo Trail robusto ','Uomo Trail leggero');
		$couterTitlesMtb = array('Uomo Enduro ','Uomo Trail robusto ','Uomo Trail leggero', 'Uomo strada premium ','Uomo strada superlight ','Uomo corsa strada ','Donna strada premium ');
		$technohref = '/it/technology/abbigliamento/Clima-Ride/Clima-Ride';
		$ergoRideCat = array('caschi', 'scarpe', 'caschi', 'abbigliamento', 'scarpe', 'abbigliamento');
		$downloadTrad = 'Scarica il catalogo 2013';
		$helmetHref = '/it/product/caschi/caschi/caschi';
		break;
	case 'es':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'DESCUBRIR LAS GAMAS';
		$couterTitlesRoad = array('Premium carretera hombre','Superlight carretera hombre','Competición carretera hombre','Premium carretera mujer','Enduro hombre','MTB duro hombre','MTB ligero hombre');
		$couterTitlesMtb = array('Enduro hombre','MTB duro hombre','MTB ligero hombre','Premium carretera hombre','Superlight carretera hombre','Competición carretera hombre','Premium carretera mujer');
		$technohref = '/es/technology/textil/Clima-Ride/Clima-Ride';
		$ergoRideCat = array('cascos', 'calzado', 'cascos', 'textil', 'calzado', 'textil');
		$downloadTrad = 'Descargar el catálogo 2013';
		$helmetHref = '/es/product/cascos/cascos/cascos';
		break;
	case 'ja':
		$a_rideBetter = 'RIDE BETTER';
		$a_climaRide = 'CLIMA RIDE';
		$a_ergoRide = 'ERGO RIDE';
		$a_energyRide = 'ENERGY RIDE';
		$a_disco = 'カテゴリーを探す';
		$couterTitlesRoad = array('メンズ プレミアムロード','メンズ スーパーライトロード','メンズ ロードレーシング','ウィメンズ プレミアムロード','メンズ エンデューロ','メンズ ハードトレイル','メンズ 軽量トレイル');
		$couterTitlesMtb = array('メンズ エンデューロ','メンズ ハードトレイル','メンズ 軽量トレイル','メンズ プレミアムロード','メンズ スーパーライトロード','メンズ ロードレーシング','ウィメンズ プレミアムロード');
		$technohref = '/ja/technology/apparel/Clima-Ride/%E3%82%AF%E3%83%AA%E3%83%9E%E3%83%A9%E3%82%A4%E3%83%89';
		$ergoRideCat = array('helmets', 'footwear', 'helmets', 'apparel', 'footwear', 'apparel');
		$downloadTrad = '2013カタログをダウンロードする。<br />（カタログの言語は英語となります）';
		$helmetHref = '/ja/product/helmets/%E3%83%98%E3%83%AB%E3%83%A1%E3%83%83%E3%83%88/helmets';
		break;
	default:
		break;
}


//Road/mtb checking and ordering
$mtbFirst = false;
if ($discipline == 'mtb' || $_GET['disc'] == 'mtb') {
	$mtbProducts = array_slice($field_landing_macromodel, 20);
	$roadProducs = array_slice($field_landing_macromodel, 0, 20);
	$macroList = array_merge($mtbProducts, $roadProducs);
	$mtbFirst = true;
	$couterTitles = $couterTitlesMtb;
} else {
	$macroList = $field_landing_macromodel;
	$couterTitles = $couterTitlesRoad;
}

$out = "";
$f = 0;
foreach ($macroList as $key => $macro) {
	$t = $key + 1;
	if (($t - 1) % 5 == 0)
		$f = 0;
	$f++;
	$href = url('node/' . $macro['nid']);
	$query1 = db_query("SELECT m.field_usp_value, n.tnid, r.body FROM {node_revisions} r INNER JOIN {content_type_macromodel} m USING (nid) INNER JOIN {node} n USING (nid) WHERE r.nid = %d", $macro['nid']);
	while ($result1 = db_fetch_array($query1)) {
		$desc = $result1['body'];
		$usp = $result1['field_usp_value'];
		$tnid = $result1['tnid'];
	}
	if ($lang != 'en') {
		$query2 = db_query("SELECT r.title FROM {node_revisions} r WHERE r.nid = %d", $tnid);
		while ($result2 = db_fetch_array($query2)) {
			$entitle = $result2['title'];
		}
	} else {
		$entitle = $macro['safe']['title'];
	}
	$macro_omniture = 'landingpage_SSapparel13_' . replace_special_char($entitle);
	$floating2 = ' flright';
	$floating1 = ' flleft';
	if (($t > 20 && !$mtbFirst) || ($t < 16 && $mtbFirst)) {
		$floating2 = ' flleft';
		$floating1 = ' flright';
	}
	if ($f == 1 || $f == 4) {
		$tempf = $floating1;
		$floating1 = $floating2;
		$floating2 = $tempf;
	}
	$hidden = ($t > 5) ? ' hideIt' : '';

	if ($f != 3)
		$out .= '<div class="statique row_' . $f . $floating1 . '"><div class="liquid hideIt"><div class="text' . $floating2 . '">';
	$out .= '<div class="txt_ctn"><h3 class="helvetica">' . $macro['safe']['title'] . '</h3>
                    <p class="subtitle">' . $usp . '</p>
                    <p class="desc">' . $desc . '</p>
                    <a class="homemore" onclick="omniture_click(this,\'' . $macro_omniture . '\')" href="' . $href . '?intcmp=' . $macro_omniture . '"> ' . t('More') . '</a>
                    </div>';
	if ($f != 2)
		$out .= '</div>';
	$ft = $t;
	if ($mtbFirst) {
		if ($t < 16) {
			$ft = $t + 20;
		} else {
			$ft = $t - 15;
		}
	}
	if ($f != 2 && $t < 6) {
		$out .= '<div class="img_ctn' . $floating1 . '"><img class="animg" src="/sites/default/files/new_landing_pages/ss2013/macro_' . $ft . '.jpg"/></div></div></div>';
	} elseif ($f != 2) {
		$out .= '<div class="img_ctn' . $floating1 . '"><img class="animg toLoad" src="/sites/default/themes/mavic/images/mavic-loader.gif" data-img="/sites/default/files/new_landing_pages/ss2013/macro_' . $ft . '.jpg"/></div></div></div>';
	}
}
?>

<script type="text/javascript" >
    if(language != 'ja') {
        Cufon.replace(".ss2012_discover", {hover: true, "font-family": "Helvetica65-Medium"});
    }
	var counterTitles = ["<?php echo implode('","', $couterTitles); ?>"];
	var currentSlide = 0;
	var currentStep = 0;
	var imgT = new Array();
	imgT[10] = -50;
	imgT[11] = 0;
	imgT[20] = -160;
	imgT[21] = -40;
	imgT[30] = -100;
	imgT[31] = 42;
	imgT[40] = -80;
	imgT[41] = 30;
    $(document).ready(function() {
        $("#body-background").ezBgResize();
        checkSize();
        //newsletter subscription
        var denternl = $('#disc_newsl_input');
        denternl.focus( function () {
            if(dfirstFocus){
                dfirstFocus=false;
                dnlvalue = denternl.val();
                denternl.val('');
            }
        }).blur( function () {
            (denternl.val().replace(/\s*/,"") == "")?denternl.val(dnlvalue):"";
        });
		var anchorA = [0,2555,3185,3755,4305];
        $('.anchor').click(function(e){
            e.preventDefault();
            var chaptarg = $(this).attr('href');
            var catTopPosition = anchorA[$(this).index()];//($(chaptarg).offset().top);

            if($.browser.mozilla || $.browser.msie){//firefox ie fix
                $('html').stop().animate({scrollTop:catTopPosition}, 600);
            } else {//chrome
                $('body').stop().animate({scrollTop:catTopPosition}, 600);
            }
        });
        $('.last_anchor').mouseover(function(){
            $('#cxr_navbar').addClass('last_active');
        });
        $('.last_anchor').mouseout(function(){
            $('#cxr_navbar').removeClass('last_active');
        });
		$('#topanchorlink').click(function (e) {
			e.preventDefault();
			if($.browser.mozilla || $.browser.msie){//firefox ie fix
                $('html').stop().animate({scrollTop:0}, 600);
            } else {//chrome
                $('body').stop().animate({scrollTop:0}, 600);
            }
		});

		//ergoRide
		$('.er_block').mouseenter(function (e) {
			var ergo = $(this).attr('id');
			$('.ergo_ride_text_block').hide();
			$(this).siblings('.' + ergo).show();
		});
		$('.ergo_ride_text_block').mouseleave(function (e) {
			$('.ergo_ride_text_block').hide();
		});

        //slideShow
		$('.prev_slide').click(function(){nextSlide('back')});
		$('.next_slide').click(function(){nextSlide('next')});
		initSlide();
		//load remaining img
		$('.toLoad').each(function (i) {
			var src = $(this).attr("data-img");
			$(this).attr("src", src).removeAttr("data-img").removeClass('toLoad');
		});
		
		//On scroll event
		var scrollPosition = 0;
		$(window).bind('scroll',function(){
            if($.browser.mozilla || $.browser.msie){//firefox ie fix
                scrollPosition = $('body, html').scrollTop();
            } else {//chrome
                scrollPosition = $('body').scrollTop();
            }

            var aciveNotSet = true;
            $('.anchor_link').each(function(i){
                switch(i){
                    case 0:
                        if (scrollPosition < anchorA[1]){
                            $(this).addClass('active');
                            activeNotSet = false;
                        } else {
                            $(this).removeClass('active');
                            activeNotSet = true;
                        }
                        break;
                    case 4:
                        if (scrollPosition > anchorA[4]){
                            $(this).addClass('active');
							
                        } else {
                            $(this).removeClass('active');
                            activeNotSet = true;
                        }
                        break;
                    default:
                        if (activeNotSet){
                            if (scrollPosition > anchorA[i-1] && scrollPosition < anchorA[i+1]){
                                $(this).addClass('active');
                                activeNotSet = false;
                            } else {
                                $(this).removeClass('active');
                            }
                        } else {
                            $(this).removeClass('active');
							
                        }
                        break;
                }
            });
            if ($('.last_anchor').hasClass('active')){
                $('#cxr_navbar').addClass('last_active');
            } else {
                $('#cxr_navbar').removeClass('last_active');
            }
			//slideshow nav bar position
			var maxTopNav = 85;
			/*if (scrollPosition < maxTopNav + 25) {
				$('#slideShow_nav').css({'top':'140px'});
			} else if (scrollPosition > (maxTopNav + 24) && scrollPosition < 2000){
				$('#slideShow_nav').css({'top':scrollPosition + 25 + 'px'});
			} */
			$('#cxr_navbar').css({'top':scrollPosition + 'px'});
        });
		

    });
	function nextSlide(way){
		var toHide = 0;
		var toShow = 0;
				<?php if ($mtbFirst) {
			print 'var lsBis = 3;';
		} else {
			print 'var lsBis = 4;';
		}?>
		switch (way) {
			case 'back':
				if (currentSlide == 0) {
					switchLandscape();
					toHide = 0;
					currentSlide = 24;
					currentStep = 6;
				} else {
					toHide = currentSlide;
					currentSlide -= 4;
					if ((currentStep) == lsBis) {
						switchLandscape();
					}
					currentStep--;					
				}				
				break;
			case 'next':
				if (currentSlide == 24) {
					switchLandscape();
					toHide = 24;
					currentSlide = 0;
					currentStep = 0;
				} else {
					toHide = currentSlide;
					currentSlide += 4;
					currentStep++;
					if ((currentStep) == lsBis) {
						switchLandscape();
					}
				}
				break;
		}
			
		for (i=0;i<4;i++) {
			$('.liquid').eq(toHide + i).fadeOut(300);
			$('.statique').eq(toHide + i).css('z-index',0);			
			var ht = '-500px';
			if ($('.liquid').eq(toHide + i).find('.img_ctn').hasClass('flright')) {
				ht = '500px';
			}
			$('.liquid').eq(toHide + i).find('.animg').css('margin-left',ht);
			var lr = 0;
			if ($('.liquid').eq(currentSlide + i).find('.img_ctn').hasClass('flright')) {
				lr = 1;
			}
			$('.liquid').eq(currentSlide + i).find('.animg').animate({'margin-left':imgT[(i+1)*10+lr]+ 'px'},800);
			$('.liquid').eq(currentSlide + i).fadeIn(500);
			var zi = (i==2 && lr == 0) ? 6 : 7;
			$('.statique').eq(currentSlide + i).css('z-index',zi);
		}
		$('.counter').html(currentStep + 1 + '/7');
		$('.counter_title').html(counterTitles[currentStep]);
		if(language != 'ja') {
			Cufon.replace(".helveticaBis", {hover: true, ignore: { ul: true }, "font-family": "Helvetica75"});//Helvetica65-Medium
		}
	}	
	function initSlide() {
		for (i=0;i<4;i++) {
			var lr = 0;
			if ($('.liquid').eq(i).find('.img_ctn').hasClass('flright')) {
				lr = 1;
			}
			$('.liquid').eq(i).fadeIn(300);
			$('.liquid').eq(i).find('.animg').animate({'margin-left':imgT[(i+1)*10+lr]+ 'px'},800);
			var zi = (i==3) ? 6 : 7;
			$('.statique').eq(i).css('z-index',zi);
			
		}
			
	}
	function switchLandscape () {
			var lsc = $('#body-background');
			var lsimg = lsc.find('img');
			var lsw = lsimg.width();
			var lsh = lsimg.height();
			var lsBis = $('.landscapeBis');
			var lsBissrc = lsBis.attr('src');
			lsBis.remove().appendTo('#body-background').attr({'width':lsw,'height':lsh}).fadeIn(800).removeClass('landscapeBis');
			lsimg.addClass('landscapeBis').fadeOut(800).remove().appendTo('#landscapeBis');
			$('#tm_img').attr('src',lsBissrc);
	}

</script>

<!-- Sharing box top right -->
<ul id="disc_toolbox">
    <li>
        <div class="share">
            <span class="helvetica"><?php print t('share'); ?></span>
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_32x32_style addthis_default_style">
                <a class="addthis_button_facebook"></a>
                <a class="addthis_button_twitter"></a>
                <a class="addthis_button_google_plusone_share"></a>
            </div>
            <script>
                var addthis_config = {
                    ui_language: '<?php echo $lang; ?>',
                    ui_click: true,
                    ui_use_css: true,
					data_track_clickback: false,
                    data_track_addressbar: false
                };
                var addthis_share = {
                    url_transforms : { clean: true, remove: ['intcmp'] }, 
                    templates: { twitter: '<?php print $title; ?> {{url}} #bike @mavic' }
                };
								
            </script>
            <!-- AddThis Button END -->
        </div>
    </li>     
    <li>
        <a class="button_disc button_catalog" href="<?php
$catalog = reset($menu_download);
print url($catalog['link']['href']);
?>?intcmp=<?php print $omnitureDownload ?>" onclick="omniture_click(this, '<?php print $omnitureDownload ?>');" target="_blank"><span class="helvetica"><?php print t('catalog') ?></span><br /><span class="share_comment"><?php print $downloadTrad; ?></span></a>
    </li> 	
    <li>
        <form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
            <a href="/<?php print $lang; ?>/newsletter?intcmp=landingpage_SSapparel13_newsletter" onclick="omniture_click(this, 'landingpage_SSapparel13_newsletter');">
                <span class="helvetica">newsletter</span>
            </a>
            <div class="whitebg share_comment">
                <input class="enternl" id="disc_newsl_input" type="text" name="adress" value="<?php print t('Enter your email'); ?>" maxlength="100" autocomplete="off">
                <input class="submitnl" type="submit" name="submitnl" value="OK">
            </div>
        </form>
    </li>   
</ul>
<!-- End Sharing box top right -->
<!-- top button -->
<a id="topanchorlink" href="body"  class="anchor helvetica topanchorlink"><?php print t('TOP'); ?></a>
<!-- End top button -->
<div id="cxr_content">
    <!-- anchor nav bar -->
    <div id="cxr_navbar">
		<a href="#ride_better" class="anchor anchor_link helvetica active <?php print $lang; ?>"><?php print $a_rideBetter; ?></a>
        <a href="#climaRideContent" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_climaRide; ?></a>
        <a href="#ergoRide" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_ergoRide; ?></a>
        <a href="#energyRide" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_energyRide; ?></a>
        <a href="#discover" class="anchor anchor_link helvetica last_anchor <?php print $lang; ?>"><?php print $a_disco; ?></a>	
    </div>
    <!-- end anchor nav bar -->
    <!-- Main title -->
	<div id="ss2012_landing_header">
        <img class="ss2013title" border="0" src="/sites/default/files/new_landing_pages/ss2013/tit_2012_springsummer_<?php echo $lang; ?>.png" />
    </div>
    <!-- end Main title -->
    <!-- slideshow nav -->
    <div id="slideShow_nav">
        <a href="javascript:void()" class="prev_slide pv_btn"></a>
        <div class="counter helvetica helveticaBis">1/7</div>
        <a href="javascript:void()" class="next_slide pv_btn"></a>
        <div class="counter_title helvetica helveticaBis"><?php print $couterTitles[0]; ?></div>
    </div>
    <!-- end slideshow nav -->
    <!-- product slideshow -->
    <div id="ride_better" class="anchor_target">
		<?php echo $out; ?>  
    </div>
    <!-- end product slideshow -->
	<!-- slideshow nav -->
    <div id="slideShow_nav2">
        <a href="javascript:void()" class="prev_slide pv_btn"></a>
        <div class="counter helvetica helveticaBis">1/7</div>
        <a href="javascript:void()" class="next_slide pv_btn"></a>
        <div class="counter_title helvetica helveticaBis"><?php print $couterTitles[0]; ?></div>
    </div>
    <!-- end slideshow nav -->

	<div id="claim">
		<img src="/sites/default/files/new_landing_pages/ss2013/tit_ridebetter_<?php print $lang; ?>.png" width="741" height="120"/>
	</div>
	<div id="climaRide" class="anchor_target">
		<img border="0" src="/sites/default/files/new_landing_pages/ss2013/tit_climaride_<?php print $lang; ?>.png" width="741" />
		<div id="climaRideContent">
			<?php
			$cr = 0;
			foreach ($field_ss2012_climaride_data as $climaride) {
				$cr++;
				$technoTitle = $climaride['safe']['title'];
				$query1 = db_query("SELECT r.body, n.tnid FROM {node} n INNER JOIN {node_revisions} r USING (nid) WHERE n.nid = %d", $climaride['nid']);
				while ($result1 = db_fetch_array($query1)) {
					$technoDesc = $result1['body'];
					$tnid = $result1['tnid'];
				}
				if ($lang != 'en') {
					$query2 = db_query("SELECT r.title FROM {node_revisions} r WHERE r.nid = %d", $tnid);
					while ($result2 = db_fetch_array($query2)) {
						$entitle = $result2['title'];
					}
				} else {
					$entitle = $technoTitle;
				}
				$techno_omniture = 'landingpage_SSapparel13_climaride_' . replace_special_char($entitle);
				?>
				<div class="cr_block">
					<img class="cr_img" src="/sites/default/files/new_landing_pages/ss2013/climaride<?php echo $cr; ?>.png" width="113" height="75" />
					<div class="clima_ride_text_block">
						<div class="title">
	<?php echo $technoTitle; ?>

						</div>
						<p class="cr_desc"><?php echo $technoDesc; ?></p>
						<p class="more">
							<a class="more" onclick="omniture_click(this,'<?php echo $techno_omniture; ?>')" href="<?php echo $technohref; ?>?intcmp=<?php echo $techno_omniture; ?>">
								<img border="0" src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
							</a>
							<a class="more" onclick="omniture_click(this,'<?php echo $techno_omniture; ?>')" href="<?php echo $technohref; ?>?intcmp=<?php echo $techno_omniture; ?>"><?php echo t('More'); ?></a>
						</p>

					</div>

				</div>



	<?php
	if ($cr % 2 == 0)
		print '<div class="clear"></div>';
}
?>
		</div>
	</div>
	<div id="ergoRide" class="anchor_target">
		<img id="ergoRide_title" border="0" src="/sites/default/files/new_landing_pages/ss2013/tit_ergoride_<?php print $lang; ?>.png" width="741" />
		<div id="ergoRideContent">
			<?php
			$er = 0;
			foreach ($field_ss2012_ergoride_data as $ergoride) {
				$er++;
				$technoTitle = $ergoride['safe']['title'];
				$query1 = db_query("SELECT r.body, n.tnid FROM {node} n INNER JOIN {node_revisions} r USING (nid) WHERE n.nid = %d", $ergoride['nid']);
				while ($result1 = db_fetch_array($query1)) {
					$technoDesc = $result1['body'];
					$tnid = $result1['tnid'];
				}
				if ($lang != 'en') {
					$query2 = db_query("SELECT r.title FROM {node_revisions} r WHERE r.nid = %d", $tnid);
					while ($result2 = db_fetch_array($query2)) {
						$entitle = $result2['title'];
					}
				} else {
					$entitle = $technoTitle;
				}
				$techno_omniture = 'landingpage_SSapparel13_ergoride_' . replace_special_char($entitle);
				$ergohref = url('node/' . $ergoride['nid']);
				$firstOnes = ($er < 4) ? ' firstOnes' : '';
				$ergoH = ($er % 2 === 0) ? '' : '<div class="ergoCol' . $firstOnes . '">';
				$ergoF = ($er % 2 === 0) ? '</div>' : '';
				print $ergoH;
				?>
				<div id="ergo_<?php print $ergoride['nid']; ?>" class="er_block">
					<img class="er_img" src="/sites/default/files/new_landing_pages/ss2013/ergoride<?php echo $er; ?>.jpg" width="245" height="163" />
					<span class="er_title helvetica"><?php echo $technoTitle; ?></span>
				</div>
				<div class="ergo_ride_text_block ergo_<?php print $ergoride['nid']; ?>" >
					<div class="title helvetica"><?php echo $technoTitle; ?><br />
						<span class="ergo_subtiltle"><?php echo $ergoRideCat[$er - 1]; ?></span>
					</div>
					<p><?php echo $technoDesc; ?></p>
					<a class="homemore" onclick="omniture_click(this,'<?php echo $techno_omniture; ?>')" href="<?php echo $ergohref; ?>?intcmp=<?php echo $techno_omniture; ?>"><?php echo t('More'); ?></a>
				</div>
				<?php print $ergoF; ?>
			<?php }
			?>
		</div>
	</div>
	<div id="energyRide" class="anchor_target">
		<img id="energyRide_title" border="0" src="/sites/default/files/new_landing_pages/ss2013/tit_energyride_<?php print $lang; ?>.png" width="741" />
		<div id="energyRideContent">
			<?php
			$er = 0;
			foreach ($field_ss2013_energyride_data as $energyride) {
				$er++;
				$technoTitle = $energyride['safe']['title'];
				$query1 = db_query("SELECT r.body, n.tnid FROM {node} n INNER JOIN {node_revisions} r USING (nid) WHERE n.nid = %d", $energyride['nid']);
				while ($result1 = db_fetch_array($query1)) {
					$technoDesc = $result1['body'];
					$tnid = $result1['tnid'];
				}
				if ($lang != 'en') {
					$query2 = db_query("SELECT r.title FROM {node_revisions} r WHERE r.nid = %d", $tnid);
					while ($result2 = db_fetch_array($query2)) {
						$entitle = $result2['title'];
					}
				} else {
					$entitle = $technoTitle;
				}
				$techno_omniture = 'landingpage_SSapparel13_energyride_' . replace_special_char($entitle);
				$ergohref = url('node/' . $energyride['nid']);
				$firstOnes = ($er < 4) ? ' firstOnes' : '';
				$ergoH = ($er % 2 === 0) ? '' : '<div class="ergoCol' . $firstOnes . '">';
				$ergoF = ($er % 2 === 0) ? '</div>' : '';
				print $ergoH;
				?>
				<div id="ergo_<?php print $energyride['nid']; ?>" class="er_block">
				<img class="er_img" src="/sites/default/files/new_landing_pages/ss2013/energyride<?php echo $er; ?>.jpg" width="245" height="163" />
				<span class="er_title helvetica"><?php echo $technoTitle; ?></span>
				</div>
				<div class="ergo_ride_text_block ergo_<?php print $energyride['nid']; ?>">
					<div class="title helvetica"><?php echo $technoTitle; ?><br />
						<span class="ergo_subtiltle"><?php echo t('footwear'); ?></span>
					</div>
					<p><?php echo $technoDesc; ?></p>
					<a class="homemore" onclick="omniture_click(this,'<?php echo $techno_omniture; ?>')" href="<?php echo $ergohref; ?>?intcmp=<?php echo $techno_omniture; ?>"><?php echo t('More'); ?></a>
				</div>
				<?php print $ergoF; ?>
			<?php }
			?>
		</div>
	</div>
	<!-- Discover -->
	<?php
	$rangeAppHref = array();
	$apparelMenu = array_pop($primary_links);
	foreach ($apparelMenu['below'] as $range) {
		$hrefA = array_shift($range['below']);
		$rangeAppHref[$range['link']['title']] = url($hrefA['link']['href']);
	}
	$rangeFootHref = array();
	$footMenu = array_pop($primary_links);

	foreach ($footMenu['below'] as $range) {
		$hrefA = array_shift($range['below']);
		$rangeFootHref[$range['link']['title']] = url($hrefA['link']['href']);
	}
	?>
	<div id="discover" class="anchor_target">
		<img class="cxr-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/discover_<?php echo $lang; ?>.gif" width="741" />
		<div class="disco_block">
			<img src="/sites/default/files/new_landing_pages/ss2013/discoHelmets.jpg" />
			<span class="helvetica subtitle"><?php print t('helmets'); ?></span>
			<ul>
				<li><a href="<?php print $helmetHref; ?>?intcmp=landingpage_SSapparel13_discover_helmets" onclick="omniture_click(this, '');"><?php print t('road') ?></a></li>
				<li><a href="<?php print $helmetHref; ?>?intcmp=landingpage_SSapparel13_discover_helmets" onclick="omniture_click(this, '');"><?php print t('mountain bike') ?></a></li>
			</ul>

		</div>	
		<div class="disco_block">
			<img src="/sites/default/files/new_landing_pages/ss2013/discoFootwear.jpg" />
			<span class="helvetica subtitle"><?php print t('footwear'); ?></span>
			<ul>
				<?php foreach ($rangeFootHref as $title => $link) : ?>
					<li><a href="<?php print $link; ?>?intcmp=landingpage_SSapparel13_discover_<?php print $title; ?>" onclick="omniture_click(this, '');"><?php print $title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>	
		<div class="disco_block">
			<img src="/sites/default/files/new_landing_pages/ss2013/discoApparel.jpg" />
			<span class="helvetica subtitle"><?php print t('apparel'); ?></span>
			<ul>
				<?php foreach ($rangeAppHref as $title => $link) : ?>
					<li><a href="<?php print $link; ?>?intcmp=landingpage_SSapparel13_discover_<?php print $title; ?>" onclick="omniture_click(this, '');"><?php print $title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>	

	</div>
	<!-- End discover -->
	<!-- Subscribe -->
	<div id="suscribe">
		<div id="nl_subscribe">
			<img border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre4_<?php echo $lang; ?>.gif" width="741" />
			<a class="cxr_nlsubcsribe" href="<?php echo url('newsletter', array('absolute' => TRUE)); ?>?intcmp=landingpage_SSapparel13_newsletter" onclick="omniture_click(this, 'landingpage_SSapparel13_newsletter');" style="background-image:url('<?php echo base_path(); ?>sites/default/themes/mavic/images/landingpage/tyres2012/signup_<?php echo $lang; ?>.gif');"></a>
		</div>
	</div>
	<!-- End subscribe -->

</div>
<?php 
$lssrc = '/sites/default/themes/mavic/images/landscapes/MTB_cross-country.jpg';
if ($mtbFirst) {
	$lssrc = '/sites/default/themes/mavic/images/landscapes/road_mountain.jpg';
}
print '<div id="landscapeBis" style="display: none;"><img class="landscapeBis" src="'.$lssrc.'" alt="Bg" style="display: none;" /></div>';
?>