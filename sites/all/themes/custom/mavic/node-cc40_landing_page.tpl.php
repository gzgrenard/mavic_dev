<?php
switch ($lang) {
	case 'en':
		$a_CC40C = 'VIDEO';
		$a_research = 'RESEARCH';
		$a_solutions = 'SOLUTIONS';
		$a_perform = 'TEST PROTOCOLS';
		$a_disco = 'TECHNICAL FEATURES';
		$href_product = '/en/product/wheels/road-triathlon/wheels/Cosmic-Carbone-40-C';
		$href_title = 'Discover the Cosmic Carbone 40 C';
		break;
	case 'fr':
		$a_CC40C = 'VIDEO';
		$a_research = 'RECHERCHES';
		$a_solutions = 'SOLUTIONS';
		$a_perform = 'PROTOCOLES DE TEST';//'PROTOCOLES DE TEST LABORATOIRES ET TERRAIN'
		$a_disco = 'CARACTERISTIQUES TECHNIQUES';
		$href_product = '/fr/product/roues/route-et-triathlon/roues/Cosmic-Carbone-40-C';
		$href_title = 'DECOUVRIR LA COSMIC CARBONE 40 C';
		break;
	case 'de':
		$a_CC40C = 'VIDEO';
		$a_research = 'FORSCHUNG';
		$a_solutions = 'LÖSUNGEN';
		$a_perform = 'TESTPROTOKOLLE';
		$a_disco = 'TECHNISCHE EIGENSCHAFTEN';
		$href_product = '/de/product/laufr%C3%A4der/rennrad-triathlon/laufr%C3%A4der/Cosmic-Carbone-40-C';
		$href_title = 'Entdecke die Cosmic Carbone 40 C';
		break;
	case 'it':
		$a_CC40C = 'VIDEO';
		$a_research = 'RICERCA';
		$a_solutions = 'SOLUZIONI';
		$a_perform = 'PROTOCOLLI DI TEST';
		$a_disco = 'CARATTERISTICHE TECNICHE';
		$href_product = '/it/product/ruote/strada-triathlon/ruote/Cosmic-Carbone-40-C';
		$href_title = 'Scopri la Cosmic Carbone 40 C';
		break;
	case 'es':
		$a_CC40C = 'VIDEO';
		$a_research = 'INVESTIGACIÓN';
		$a_solutions = 'SOLUCIONES';
		$a_perform = 'PROTOCOLO DE PRUEBAS';
		$a_disco = 'CARACTERÍSTICAS TÉCNICAS';
		$href_product = '/es/product/ruedas/carretera-y-triatl%C3%B3n/ruedas/Cosmic-Carbone-40-C';
		$href_title = 'Descubre la Cosmic Carbone 40 C';
		break;
	case 'ja':
		$a_CC40C = 'ビデオ';
		$a_research = 'リサーチ';
		$a_solutions = '解決策';
		$a_perform = 'フィールド＆ラボテスト';
		$a_disco = 'テクニカルフィーチャー';
		$href_product = '/ja/product/wheels/%E3%83%AD%E3%83%BC%E3%83%89%EF%BC%86%E3%83%88%E3%83%A9%E3%82%A4%E3%82%A2%E3%82%B9%E3%83%AD%E3%83%B3/wheels/%E3%82%B3%E3%82%B9%E3%83%9F%E3%83%83%E3%82%AF%E3%82%AB%E3%83%BC%E3%83%9C%E3%83%B340C';
		$href_title = 'コスミック カーボン 40Cとは';
		break;
	default:
		break;
}
?>
<script type="text/javascript">
	var dnlvalue,dfirstFocus=true;
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
		//anchor scrolling
		var anchorA = new Array();
		$('.anchor_target').each(function(){
			anchorA.push(($(this).offset().top) - 80);
		});
		$('.anchor').click(function(e){
			e.preventDefault();
			var chaptarg = $(this).attr('href');
			var catTopPosition = ($(chaptarg).offset().top) - 70;
			if($.browser.mozilla || $.browser.msie){//firefox ie fix
				$('html').stop().animate({scrollTop:catTopPosition}, 600);
			} else {//chrome
				$('body').stop().animate({scrollTop:catTopPosition}, 600);
			}
		});
		$(window).bind('scroll',function(){
			if($.browser.mozilla || $.browser.msie){//firefox ie fix
				var scrollPosition = $('body, html').scrollTop();
			} else {//chrome
				var scrollPosition = $('body').scrollTop();
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
					case 5:
						if (scrollPosition > anchorA[5]){
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
				$('#CC40C_navbar').addClass('last_active');
			} else {
				$('#CC40C_navbar').removeClass('last_active');
			}
		});
		$('.last_anchor').mouseover(function(){
			$('#CC40C_navbar').addClass('last_active');
		});
		$('.last_anchor').mouseout(function(){
			$('#CC40C_navbar').removeClass('last_active');
		});
		if(language != 'ja') {
			Cufon.replace(".helvetica75", {
				hover: true, 
				ignore: {
					ul: true
				}, 
				"font-family": "Helvetica65-Medium"
			});
		}
	
	});
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
					data_track_addressbar: false
				};
				var addthis_share = {
					url_transforms : { clean: true, remove: ['intcmp'] }, 
					templates: { twitter: 'Cosmic Carbone 40C - the first reliable carbon clincher wheel. {{url}} #bike #CC40C @mavic' }
				};
								
			</script>
			<!-- AddThis Button END -->
		</div>
	</li>     
	<li>
		<a class="button_disc button_catalog" href="<?php $catalog = reset($menu_download);
print url($catalog['link']['href']); ?>?intcmp=landingpage_CC40C_download" onclick="omniture_click(this, 'landingpage_CC40C_download');" target="_blank"><span class="helvetica"><?php print t('catalog') ?></span><br /><span class="share_comment"><?php print t('download the 2013 catalog'); ?></span></a>
	</li> 	
	<li>
		<form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
			<a href="/<?php print $lang; ?>/newsletter?intcmp=landingpage_CC40C_newsletter" onclick="omniture_click(this, 'landingpage_CC40C_newsletter');">
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
<div id="CC40C_content">
	<!-- anchor nav bar -->
	<div id="CC40C_navbar">
		<a href="#video_CC40C" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_CC40C; ?></a>
		<a href="#CC40C_research" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_research; ?></a>
		<a href="#CC40C_technos" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_solutions; ?></a>
		<a href="#CC40C_process" class="anchor anchor_link helvetica <?php print $lang; ?>"><?php print $a_perform; ?></a>
		<a href="#CC40C_discover" class="anchor anchor_link helvetica last_anchor <?php print $lang; ?>"><?php print $a_disco; ?></a>	
	</div>
	<!-- end anchor nav bar -->
	<!-- First diaporama -->
	<?php $bgcolor = 'blackbg'; //$bgcolor='whitebg';
	?>
	<div id="landing_diaporama" class="anchor_target">
		<div id="diapos" class="<?php print $bgcolor; ?>">
			<?php for ($i = 1; $i <= 3; $i++) : ?>
				<img class="diapo" src="/sites/default/themes/mavic/images/landingpage/cc40c/intro_visuel<?php print $i; ?>.jpg?v2" width="741" height="693" />		
			<?php endfor ?>
		</div>
		<div id="landing_title">
			<img class= "CC40C" alt="<?php print $title; ?>" src="/sites/default/themes/mavic/images/landingpage/cc40c/cc40c_<?php print $lang; ?>.png" />						
			<div class="nav nav-left"><div id="btn-left"><!-- --></div></div>
			<div class="nav nav-right"><div id="btn-right"><!-- --></div></div>
		</div>
	</div>
	<!-- end First diaporama -->
	<!-- Video -->
	<?php
	if ($tnid != $nid) {
		$english_node = node_load($tnid);
	} else {
		$english_node = $node;
	}
	$videoId = $field_vimeo_url[0]['data']['id'];
	?>
	<div id="video_CC40C" class="anchor_target">
		<div class="flash_content" id="player_<?php print $nid; ?>">
			<iframe id="vimeo_<?php print $videoId; ?>" src="http://player.vimeo.com/video/<?php print $videoId; ?>?title=0&amp;byline=0&amp;portrait=0&amp;color=FFE500&amp;api=1&amp;autoplay=0&amp;player_id=vimeo_<?php print $videoId; ?>" width="741" height="418" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		</div>
		<div class="poster_video">
			<img src="<?php print base_path() . path_to_theme() . '/images/landingpage/cc40c/video_' . $lang . '.jpg'; ?>" width="741" height="418" />		
			<a href="javascript:void(0)" onclick="showVideo();$f('vimeo_<?php print $videoId; ?>').api('play')" class="homeplay"></a>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo base_path() . path_to_theme(); ?>/js/froogaloop.min.js"></script>
	<script type="text/javascript">
		(function(){
			// Listen for the ready event for any vimeo video players on the page
			var player = document.querySelector('iframe');
			$f(player).addEvent('ready', ready);

			/**
			 * Utility function for adding an event. Handles the inconsistencies
			 * between the W3C method for adding events (addEventListener) and
			 * IE's (attachEvent).
			 */
			function addEvent(element, eventName, callback) {
				if (element.addEventListener) {
					element.addEventListener(eventName, callback, false);
				}
				else {
					element.attachEvent(eventName, callback, false);
				}
			}

			/**
			 * Called once a vimeo player is loaded and ready to receive
			 * commands. 
			 */
			function ready(player_id) {
				var froogaloop = $f(player_id);
				var GAurl = "<?php print 'play/' . str_replace(" ", "-", $english_node->field_vimeo_url[0]['data']['title']); ?>",
				mediaName = "<?php print str_replace(" ", "-", $english_node->field_vimeo_url[0]['data']['title']); ?>",
				mediaLength = 0,
				mediaPlayerName = "CC40C_landing_page",
				mediaOffset = 0,
				mediaFirstime = true;
				s.eVar15 = "<?php print $lang ?>";
				//vimeo event
				froogaloop.addEvent('loadProgress', function(data) {
					if (mediaFirstime) {
						mediaLength = data.duration;
						startMovie();
						mediaFirstime = false;
					}       
				});
				froogaloop.addEvent('playProgress', function(data) {
					mediaOffset = data.seconds;

				});

				froogaloop.addEvent('play', function(data) {
					if(!mediaFirstime){
						playMovie();
					}

				});
				froogaloop.addEvent('pause', function(data) {
					stopMovie();
				})
				froogaloop.addEvent('finish', function(data) {
					endMovie();
				});
				froogaloop.addEvent('seek', function(data) {
					stopMovie();
					mediaOffset = data.seconds;
				});
				function startMovie(){	
					s.Media.open(mediaName,mediaLength,mediaPlayerName);
					s.Media.track(mediaName);
					playMovie();
					//GA
					_gaq.push(['_trackPageview', GAurl]);
				}
				function playMovie(){
					s.Media.play(mediaName,mediaOffset);
				}
				function stopMovie(){
					s.Media.stop(mediaName,mediaOffset);
				}
				function endMovie(){
					stopMovie();
					s.Media.close(mediaName);
				}
			}
		})();
	</script>
	<div class="clear"></div>
	<!-- end Video -->
	<a href="#CC40C_discover" title="<?php echo $href_title; ?>" class="discover_product"><?php echo $href_title; ?></a>
	<!-- Researches -->
	<div id="CC40C_research" class="anchor_target">
		<img class="CC40C-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/research_<?php echo $lang; ?>.gif" width="741" />
		<?php echo $body; ?>
	</div>
	<!-- endResearches -->
	<a href="#CC40C_discover" title="<?php echo $href_title; ?>" class="discover_product"><?php echo $href_title; ?></a>
	<!-- Technologies -->
	<div id="CC40C_technos" class="anchor_target">
		<img class="CC40C-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/solutions_<?php echo $lang; ?>.gif" width="741" />
		<p class="intro">
		<?php
		print $field_landing_textarea2[0]['value'];
		$t = 0;
		?></p><?php
		foreach ($field_landing_techno_desc as $techno) {

			$technoNode = node_load($field_technologienode[$t]['nid']);
			$technoHref = drupal_get_path_alias('node/' . $technoNode->nid);
			$omniture = 'landingpage_CC40C_' . $technoNode->title;
			?>
			<div class="techno_block_CC40C">
				<?php print '<img class="fleft" src="/sites/default/themes/mavic/images/landingpage/cc40c/techno_' . $t . '.jpg" />'; ?>
				<h3 class="helvetica75 fleft"><?php print $field_landing_techno_subtitle[$t]['value']; ?></h3>
				<?php print $field_landing_techno_desc[$t]['value']; ?>
				<?php if ($t != 1) : ?>
				<p class="more fleft">
					<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture; ?>" onclick="omniture_click(this,'<?php print $omniture ?>')">
						<img border="0" src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
					</a>
					<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture; ?>" onclick="omniture_click(this,'<?php print $omniture ?>')">
						<?php print t('More info'); ?>
					</a>
				</p>
				<?php endif; ?>
			</div>
			<div class="clear"></div>
			<?php $t++;
		}
		?>
	</div>
	<!-- end Technologies -->
	<a href="#CC40C_discover" title="<?php echo $href_title; ?>" class="discover_product"><?php echo $href_title; ?></a>
	<!-- Field lab & test -->
	<div id="CC40C_process" class="anchor_target">
		<img class="CC40C-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/protocols_<?php echo $lang; ?>.gif" width="741" />
		<p>
<?php print $field_landing_textarea3[0]['value']; ?>
		</p>
		<script type="text/javascript" src="<?php echo base_path() . path_to_theme(); ?>/js/lightbox.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#grid a').lightBox();
			});
			
		</script>
		<div id="CC40C_landing_diaporama">
			<div id="grid">
				<?php
				$i = 0;
				foreach ($field_landing_diapo_titre as $altDiapo) {
					$titleDiapo = $altDiapo['value'];
					$descDiapo = $field_landing_diapo_desc[$i]['value'];
					(($i == 2) || ($i == 4) || ($i == 8) || ($i == 9)) ? $lastOne = ' lastone' : $lastOne = '';

					if ($i == 5) {
						print '<div class="column left">';
					}
					?>
					<a  class="feature-image-notooltip<?php print $lastOne;
					($i == 0 || $i == 7 ) ? print ' big' : print ' small'  ?>" title="" desc="" href="<?php print base_path() . path_to_theme() . '/images/landingpage/cc40c/imgx/i' . ($i + 1) . '.jpg?v=1'; ?>"><img src="<?php print base_path() . path_to_theme() . '/images/landingpage/cc40c/thumbx/t' . ($i + 1) . '.jpg'; ?>" class="<?php ($i == 0 || $i == 7 ) ? print 'big' : print 'small'  ?>" /></a>
					<?php
					if ($i == 6) {
						print '</div>';
					}
					$i++;
				}
				?>
			</div>
			<div class="clear"></div>
		</div>

		<div id="CC40C_diapo_text" >
			<div class="fleft">
				<?php print $field_landing_textarea5[0]['value']; ?>
			</div>
			<div class="fleft">
				<?php print $field_landing_textarea4[0]['value']; ?>
			</div>
		</div>
	</div>
	<!-- end Field lab & test -->
	<a href="#CC40C_discover" title="<?php echo $href_title; ?>" class="discover_product"><?php echo $href_title; ?></a>
	<!-- Discover -->
	<div id="CC40C_discover" class="anchor_target">
		<div id="discover_range">
			<img class="CC40C-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/discover_<?php echo $lang; ?>.gif" width="741" />
			<a href="<?php print $href_product; ?>?intcmp=landingpage_CC40C_product" onclick="omniture_click(this, '');" style="background-image:none; width:100%;"><img border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/disc_<?php echo $lang; ?>.jpg" width="741" height="230" alt="<?php echo $href_title; ?>" /></a>
		</div>
	</div>
	<!-- End discover -->
	<!-- Subscribe -->
	<div id="CC40C_suscribe">
		<div id="nl_subscribe">
			<img class="CC40C-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/cc40c/titre_newsletter_<?php echo $lang; ?>.gif" width="741" />
			<a class="CC40C_nlsubcsribe" href="<?php echo url('newsletter', array('absolute' => TRUE)); ?>?intcmp=landingpage_CC40C_newsletter" onclick="omniture_click(this, 'landingpage_CC40C_newsletter');" style="background-image:url('<?php echo base_path(); ?>sites/default/themes/mavic/images/landingpage/tyres2012/signup_<?php echo $lang; ?>.gif');"></a>
		</div>
	</div>
	<!-- End subscribe -->
</div>