		<?php 
			/*google analytics*/
			if( $_SERVER['HTTP_HOST'] == 'www.mavic.com' || $_SERVER['HTTP_HOST'] == 'roadcycling.mavic.com' || $_SERVER['HTTP_HOST'] == 'triathlon.mavic.com' || $_SERVER['HTTP_HOST'] == 'mtb.mavic.com' ){ $ua =  'UA-2489222-1' ; }
			else { $ua = 'empty'; }
//			$ua =  'UA-2489222-1' ;
			/* ecriture url pour ga */

			$path = drupal_is_front_page() ? '<front>' : $_GET['q'];
			$languages = language_list('enabled');
			$options = array();
			foreach ($languages[1] as $lang_item) {
			$options[$lang_item->language] = array(
			  'href'       => $path,
			  'title'      => $lang_item->native,
			  'language'   => $lang_item,
			);
			}
			drupal_alter('translation_link', $options, $path);

			/* Here we theme our own dropdown */
			$str= substr(url($options['en']['href'], $options['en']),3);
			if($str == '') $str = '/home';
			$str='/'.$language->language.$str;
			if($node != null){
				switch($node->type){
				case "macromodel" :
					$str=str_replace("/product/","/products/productsheet/",$str);
				case "family" :
					$str=str_replace('/road-triathlon/','/road&triathlon/',strtolower($str));
					$str=str_replace('/vests/jackets/','/vests&jackets/',$str);
					$str=str_replace('/underwear/undershort/','/underwear&undershort/',$str);
					$str=str_replace('/headwear/warmers/','/headwear&warmers/',$str);
					$str=str_replace('/booties/socks/','/booties&socks/',$str);
					$str=str_replace('/mountain-bike/','/mountainbike/',$str);
					$str=str_replace("/product/","/products/range/",$str);
					$tab=split('/',$str);
					if($tab[2]=="products"){
						if($tab[4]==$tab[6]){
							array_splice($tab,6,1);
							if($tab[4]==$tab[5]){
								array_splice($tab,5,1);
							}
						}
					}
					$str=join('/',$tab);
					break;
				case "prodvalcarac" :
				case "technoline" :
					$str=str_replace('/ergo-ride/','/ergoride/',$str);
					$str=str_replace('/energy-ride/','/energyride/',$str);
					$str=str_replace('/clima-ride/','/climaride/',$str);
					$str=str_replace("/technology/","/technologies/",$str);
					break;
				case "range_landing_page" :
					$str=str_replace('/2013-helmet-range','/landing-page/helmets2013',$str);
					$str=str_replace('/a-crossmax-for-every-ride','/landing-page/crossmax2012',$str);
					break;
				case "tyre_landing_page" :
					$str=str_replace('/2012-mavic-tyres-3-years-of-development','/landing-page/tyres',$str);
				case "cc40_landing_page" :
					$str=str_replace('/cosmic-carbone-40-C-the-first-reliable-carbon-clincher-wheel','/landing-page/CC40C',$str);
					break;
				case "crossmax_landing_page" :
					$str=str_replace('/29ers-three-more-inches-of-mavic-finest-mtb-know-how','/landing-page/crossmax29-2013',$str);
					break;
				case "cxr_landing_page" :
					$str=str_replace('/Cosmic-CXR-80','/landing-page/cxr80',$str);
					break;
				case "ss2012_range_landing_page" :
					$str=str_replace('/2012-fall-winter-apparel-range','/landing-page/FWapparel2012',$str);
					break;
				case "ss2013_landing_page" :
					$str=str_replace('/spring-summer-13-apparel-footwear-helmets','/landing-page/SSapparel2013',$str);
					break;
				case "discipline_home_page" :
					$str=str_replace('/road-home','/home',$str);
					$str=str_replace('/mtb-home','/home',$str);
					break;
				case "contest" :
					$str=str_replace('/paris-roubaix-contest-2013','/contest/parisroubaix2013',$str);
					break;
				default:
					$str=str_replace('/mtb/','/mountainbike/',$str);
					$str=str_replace('/assistance/','/sport/assistance/',$str);
					$str=str_replace('/athletes/','/sport/athletes/',$str);
					$str=str_replace('/mp3-program-at-1euro','/landing-page/MP32012offer',$str);
				}
				$omniture_str = $str;
			}
			else{
				$omniture_str = $str;
				if($template_files[0] == "page-search") {
					$explodeStr = explode('/search/google_cse_adv/',$str);
					if(count($explodeStr)>1){
						$tabExplodeStr=explode('%20more%3A',$explodeStr[1]);
// a voir si besoin ou pas	if(count($tabExplodeStr)==1) $tabExplodeStr[]='all';
						$explodeStr[1] = implode('&cat=',$tabExplodeStr);
					}
					$str=implode('/search/?query=',$explodeStr);
					$omniture_str = $explodeStr[0].'/search/';
				}
			}
			switch ($discipline) {
				case "road" : 
					$str= substr_replace($str, "/road", 3, 0);		
				break;
				case "mtb" : 
					$str= substr_replace($str, "/mountainbike", 3, 0);		
				break;
				case "triathlon" : 
					$str= substr_replace($str, "/triathlon", 3, 0);		
				break;
				default: 
					$str= substr_replace($str, "/main", 3, 0);		
				break;
			}
			$omniture_str = substr($omniture_str,4);
			$omniture_str = str_replace('/',':',$omniture_str);
			$hierarchie_omniture = explode(':',$omniture_str);
		?>
                <script type="text/javascript"> Cufon.now();//prevent delay in some browsers </script>
		<script type="text/javascript">
			window['___gcfg'] = { google_analytics: false };
			window['___jsl'] = { google_analytics: false };
			
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo $ua ?>'],['_setDomainName', 'mavic.com'],['_trackPageview','<?php echo $str ?>'],['_trackPageLoadTime','<?php echo $str ?>']);
			
			<?php if(isset($_REQUEST['tpl_404'])) : ?>
				//GA tracking for 404
				_gaq.push(['_trackEvent', 'Error', '404', 'page: <?php print filter_xss($_REQUEST['tpl_404']) ?>']);
			<?php endif; ?>
			<?php if((isset($_GET['newsletterok'])) && ($_GET['newsletterok']==1)) : ?>
				<?php if((isset($_GET['contest'])) && !empty($_GET['contest'])) : ?>
				//GA tracking for newsletter contest 
				_gaq.push(['_trackEvent', 'newsletter_contest_<?php print filter_xss($_GET['contest']) ?>', 'Register', '<?php print $lang; ?>']);
				<?php else : ?>
				//GA tracking for newsletter
				_gaq.push(['_trackEvent', 'Newsletter', 'Register', '<?php print $lang; ?>']);	
				<?php endif; ?>
			<?php endif; ?>
			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		
		<!-- SiteCatalyst code version: H.19.3.
		Copyright 1997-2009 Omniture, Inc. More info available at
		http://www.omniture.com -->
		<script language="JavaScript" type="text/javascript"><!--
		/* You may give each page an identifying name, server, and channel on
		the next lines. */
			<?php if(!isset($_REQUEST['tpl_404'])) : ?>
		s.pageName="<?php echo $omniture_str ?>";
		s.channel="<?php echo $hierarchie_omniture[0] ?>";
		s.prop1="<?php if($hierarchie_omniture[1]) echo $hierarchie_omniture[1]; ?>";
		s.prop2="<?php if($hierarchie_omniture[2]) echo $hierarchie_omniture[2]; ?>";
		s.prop3="<?php if($hierarchie_omniture[3]) echo $hierarchie_omniture[3]; ?>";
		s.prop4="";
		s.prop12="<?php echo $lang;?>";
		s.prop31="<?php if (!empty($discipline)) print $discipline.'.'?>mavic.com";
		<?php 
			if($template_files[0] == "page-search" && !isset($_REQUEST['tpl_404'])) { 
				$keywords = substr($_GET['q'],strrpos($_GET['q'],'/')+1);
				if($GLOBALS['pager_total_items'][0] > 0)
					echo "s.prop5=\"$keywords\";";
				else {
					echo "s.prop5=\"null:$keywords\";";
					echo "s.prop6=\"$keywords\";";
				}
			}
			
			if(isset($_GET['newsletterok'])) { 
				?>
				s.eVar1='newsletter subscription';
				s.events='event2';
				<?php 
			}
			
			
			switch($node->type) {
				case "macromodel" :
		?>
					s.events="prodView,event7";
					s.products="<?php echo substr($str,strrpos($str,'/')+1);?>";
					s.eVar11=s.prop2+' > '+s.prop3;
		<?php
				break;
				case "family" :
		?>
					s.eVar11=s.prop2+'>'+s.prop3;
		<?php
				break;
				case "dolist" :
		?>
				s.events="event1";
		<?php 
				break;
			}
		?>
			<?php else : ?>
				s.pageType="errorPage";
			<?php endif; ?>
				
		/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
		var s_code=s.t();if(s_code)document.write(s_code)
		//--></script>
		<!-- End SiteCatalyst code version: H.19.3. -->
		<script>
			/* AddThis configuration http://www.addthis.com/help/client-api */
			if(!addthis_config) {
					var addthis_config = {
						data_track_addressbar: false,
                                                ui_language: '<?php echo $lang; ?>',
						ui_click: true,
						ui_use_css: false
				};
			}

			if(!addthis_share) {
					var addthis_share = {
                                                url_transforms : { clean: true, remove: ['intcmp'] }, 
						templates: { twitter: '{{title}} {{url}} @Mavic' }
				};
			}
		</script>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c84dda13565271e"></script>
		<script type="text/javascript">
		// Alert a message when the user shares somewhere
		function eventHandler(evt) {
			switch (evt.type) {
				case "addthis.menu.share":
					var urlshareO,urlshare='<?php echo $str ?>';
					if( typeof current_news != 'undefined' ){
						urlshareO = $("#addthis_container_"+current_news);
						if(urlshareO.length>0){
							urlshare = urlshareO.attr('addthis:url');
							urlshare = urlshare.substring(urlshare.indexOf('mavic.com/')+9);
						}
					}
					var nameOmin = 'Share_'+evt.data.service+':'+urlshare;
					omniture_click(this, nameOmin);
					switch(evt.data.service){
						case "facebook" : _gaq.push(['_trackSocial', 'facebook', 'share', urlshare,'<?php echo $str ?>']);break;
						case "twitter" : _gaq.push(['_trackSocial', 'twitter', 'share', urlshare,'<?php echo $str ?>']);break;
						case "google_plusone" : _gaq.push(['_trackSocial', 'Google', '+1', urlshare,'<?php echo $str ?>']);break;
						case "google_unplusone" : _gaq.push(['_trackSocial', 'Google', '-1', urlshare,'<?php echo $str ?>']);break;
						default:
							_gaq.push(['_trackSocial', evt.data.service, 'addthis', urlshare,'<?php echo $str ?>']);
					}
					break;
			}
		}
		function pagetracker_trackPageview(name){
			//console.log("bi-media player is playing : " + name);
		}
		// Listen to various events
		addthis.addEventListener('addthis.menu.share', eventHandler);

		</script>
	</body>
</html>