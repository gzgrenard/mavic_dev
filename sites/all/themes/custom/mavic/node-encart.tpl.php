<?php
switch($field_type_encart[0]['value']) {
	
	case 1 : // video ***********************************
		$urlValueProb = $field_url_encart[0]['value'];
		if(is_numeric($urlValueProb)) {
			$videoDataComp = array(
				'26443230' => array(
					'en' => array('26443230','A Crossmax for every ride'),
					'fr' => array('34527870','Une Crossmax pour chaque pratique'),
					'de' => array('34527593','Ein Crossmax für jedes Gelände'),
					'it' => array('34528166','Una Crossmax per ogni pratica'),
					'ja' => array('34528533','/あらゆるＭＴＢのフィールドへ、クロスマックス'),
					'es' => array('34530002','Unas Crossmax para cada biker')
				),
				'30369540' => array(
					'en' => array('30369540','Tour de France - Garmin-Cervelo wins Tour TTT'),
					'fr' => array('34499542',"Tour de France - Garmin-Cervélo gagne le contre-la-montre par équipe"),
					'de' => array('34371972','Tour de France - Garmin-Cervelo gewinnt das Tour Mannschaftszeitfahren'),
					'it' => array('34504297','Tour de France - Garmin-Cervélo vince la cronometro a squadre'),
					'ja' => array('34504574','ツール・ド・フランス　ガーミン-サーヴェロがチームTTで勝利'),
					'es' => array('34505353','Tour de France - Garmin-Cervelo, ganadores de la contrarreloj por equipo')
				),
				'28706982' => array(
					'en' => array('28706982','Birth of the Mavic helmets'),
					'fr' => array('34364004','La naissance des casques Mavic'),
					'de' => array('34366781','Geburt der Mavic Helme'),
					'it' => array('34367058','La nascita dei caschi Mavic'),
					'ja' => array('34367303','MAVICヘルメットの誕生'),
					'es' => array('34367906','Así han nacido los cascos Mavic')
				),
				'34382389' => array(
					'en' => array('34382389','Helmets technology'),
					'fr' => array('34453992','Technologie Casques'),
					'de' => array('34461198','Helm Technologie'),
					'it' => array('34461898','Tecnologia Caschi'),
					'ja' => array('34508758','Helmets technology'),
					'es' => array('34506098','Tecnología Cascos')
				)
			);
			if($tnid != $nid) {
				switch ($urlValueProb) {
					case 26443230 :
					case 30369540 :
					case 28706982 :
					case 34382389 :
						$urlValue = $videoDataComp[$urlValueProb][$lang][0];
						$encTitle = $videoDataComp[$urlValueProb][$lang][1];
						$engTitle = $videoDataComp[$urlValueProb]['en'][1];
					break;
					default :
						$urlValue = $urlValueProb;
						$english_node = node_load($tnid);
						$encTitle = $node->content['body']['#value'];
						$engTitle = $english_node->content['body']['#value'];
					break;
				}				
			} else {
				$urlValue = $urlValueProb;
				$encTitle = $engTitle = $node->content['body']['#value'];
			}
		} else { //in case it's a video uploaded at the time of the old player
			$videoData = array(
				'aerodynamic-wheel-development-with-garmin-transition-part-1' => array(
					'en' => array('9881063','Aerodynamic wheel development with Garmin-Transition (part 1)'),
					'fr' => array('9881063',"Développement de roues aérodynamiques avec l'équipe Garmin-Transition (1ère partie)"),
					'de' => array('9881063','Prototypen-Entwicklung mit dem Team Garmin-Transitions'),
					'it' => array('9881063','Sviluppo prototipi con il Team Garmin-Transition'),
					'ja' => array('9881063','ガーミン-トランジションとプロトタイプの開発'),
					'es' => array('9881063','Desarrollo de prototipos con el equipo Garmin-Transitions')
				),
				'paris-roubaix-from-inside-the-mavic-service-course' => array(
					'en' => array('11101230','Paris-Roubaix from inside the Mavic Service Course'),
					'fr' => array('11101230','Paris-Roubaix vécu par le Service Course Mavic'),
					'de' => array('11101230','Paris-Roubaix aus Sicht des Mavic Service Course'),
					'it' => array('11101230','La Parigi-Roubaix vissuta dal Service Course Mavic'),
					'ja' => array('11101230','パリ～ルーベをレースの内側から'),
					'es' => array('11101230','Paris Roubaix: así lo vive el Mavic Service Course')
				),
				'r-d-on-mavic-dh-wheels-with-fabien-barel' => array(
					'en' => array('12044105','R&D on Mavic DH wheels with Fabien Barel'),
					'fr' => array('12044105','Développement des produits SSC : collaboration entre Mavic et Fabien Barel'),
					'de' => array('12044105','Test & Entwicklung von DH-Laufrädern mit Fabien Barel'),
					'it' => array('12044105','Ricerca e sviluppo sulle ruote DH Mavic con Fabien Barel'),
					'ja' => array('12044105','ダウンヒルホイールの研究開発に協力するフェビアン・バレル'),
					'es' => array('12044105','I+D en ruedas de descenso (DH) con Fabien Barel')
				),
				'aerodynamic-wheel-development-with-garmin-transition-part-2' => array(
					'en' => array('13261930','Aerodynamic wheel development with Garmin-Transition (part 2 - windtunnel)'),
					'fr' => array('13261930',"Développement de roues aérodynamiques avec l'équipe Garmin-Transition (2e partie - soufflerie)"),
					'de' => array('13261930',"Aero-Laufrad-Entwicklung mit dem Team Garmin-Transition"),
					'it' => array('13261930','Sviluppo ruota aerodinamica con Garmin-Transition'),
					'ja' => array('13261930','エアロホイール開発のため、ガーミン-トランジションと共にテストを実施'),
					'es' => array('13261930','Desarrollo de ruedas aerodinámicas con el Garmin-Transition')
				),
				'inside-look-mavic-and-julien-absalon-test-mtb-prototypes' => array(
					'en' => array('21199489','Inside look: Mavic and Julien Absalon test MTB prototypes'),
					'fr' => array('34530481','Mavic et Julien Absalon testent les prototypes de roues  VTT'),
					'de' => array('34530793','Mavic Inside: Julien Absalon testet MTB Prototypen'),
					'it' => array('21199489',"Visto dall'interno: Mavic e Julien Absalon in test con i nuovi prototipi di ruote MTB"),
					'ja' => array('34531038','MTBプロトタイプのテストをMAVICと行う、ジュリアン・アブサロン'),
					'es' => array('34531254','Desde dentro: Mavic y Julien Absalon prueban los prototipos de ruedas para MTB')
				),
				'hushovd_classics' => array(
					'en' => array('21598590','Thor Hushovd prepares for the Paris-Roubaix'),
					'fr' => array('21598590','Thor Hushovd se prépare pour le Paris-Roubaix'),
					'de' => array('21598590','Thor Hushovd bereitet sich auf Paris-Roubaix vor'),
					'it' => array('21598590','Thor Hushovd si prepara per la Paris-Roubaix'),
					'ja' => array('21598590','Thor Hushovd prepares for the Paris-Roubaix'),
					'es' => array('21598590','Thor Hushovd se prepara para la Paris-Roubaix')
				),
				'paris-roubaix-2011-assistance' => array(
					'en' => array('22401915','Paris-Roubaix - Mavic at the heart of the action'),
					'fr' => array('34754474',"Paris-Roubaix - Mavic au cœur de l'action"),
					'de' => array('34754281','Paris-Roubaix, Mavic als Teil des Rennens'),
					'it' => array('22401915',"Paris-Roubaix - Mavic nel cuore dell'azione"),
					'ja' => array('34754974','パリ～ルーベ／マヴィックの情熱'),
					'es' => array('34755678','Paris-Roubaix - Mavic en el centro de la acción')
				),
				'paris_roubaix_challenge' => array(
					'en' => array('22998772','Amateurs challenge the Paris-Roubaix cobbles'),
					'fr' => array('34531672','Les amateurs défient les pavés de Paris-Roubaix'),
					'de' => array('34532123','Amatuere fordern die Paris-Roubaix heraus'),
					'it' => array('22998772','Amateurs challenge the Paris-Roubaix cobbles'),
					'ja' => array('34532391','石畳を走り抜ける「パリ～ルーベ チャレンジ」'),
					'es' => array('34532653','Los aficionados desafían los adoquines de Paris-Roubaix')
				),
				'san_diego' => array(
					'en' => array('23688072','Mavic-Cervelo partnership starts in San Diego'),
					'fr' => array('34752900','Le partenariat Mavic-Cervélo commence à San Diego'),
					'de' => array('34753178','Mavic Cervelo Partnerschaft beginnt in San Diego'),
					'it' => array('23688072','Mavic-Cervelo partnership starts in San Diego'),
					'ja' => array('34753423','マヴィック×サーヴェロサンディエゴでの風洞実験'),
					'es' => array('34753774','La colaboración entre Mavic y Cervélo comienza en San Diego')
				),
				'recherche_aero' => array(
					'en' => array('25860608','R&D process on aero wheels'),
					'fr' => array('34751137','Recherche et développement sur les roues aérodynamiques'),
					'de' => array('34751368','R&D Entwicklunsgprozess an Aero-Laufrädern'),
					'it' => array('34751744','Ricerca e sviluppo sulle ruote aerodinamiche'),
					'ja' => array('34752173','エアロホイールが誕生する瞬間'),
					'es' => array('34752660','Investigación y desarrollo sobre ruedas aerodinámicas')
				),
				'crossmax' => array(
					'en' => array('26443230','A Crossmax for every ride'),
					'fr' => array('34527870','Une Crossmax pour chaque pratique'),
					'de' => array('34527593','Ein Crossmax für jedes Gelände'),
					'it' => array('34528166','Una Crossmax per ogni pratica'),
					'ja' => array('34528533','/あらゆるＭＴＢのフィールドへ、クロスマックス'),
					'es' => array('34530002','Unas Crossmax para cada biker')
				),
				'TDF' => array(
					'en' => array('30369540','Tour de France - Garmin-Cervelo wins Tour TTT'),
					'fr' => array('34499542',"Tour de France - Garmin-Cervélo gagne le contre-la-montre par équipe"),
					'de' => array('34371972','Tour de France - Garmin-Cervelo gewinnt das Tour Mannschaftszeitfahren'),
					'it' => array('34504297','Tour de France - Garmin-Cervélo vince la cronometro a squadre'),
					'ja' => array('34504574','ツール・ド・フランス　ガーミン-サーヴェロがチームTTで勝利'),
					'es' => array('34505353','Tour de France - Garmin-Cervelo, ganadores de la contrarreloj por equipo')
				),
				'casque' => array(
					'en' => array('28706982','Birth of the Mavic helmets'),
					'fr' => array('34364004','La naissance des casques Mavic'),
					'de' => array('34366781','Geburt der Mavic Helme'),
					'it' => array('34367058','La nascita dei caschi Mavic'),
					'ja' => array('34367303','MAVICヘルメットの誕生'),
					'es' => array('34367906','Así han nacido los cascos Mavic')
				),
				'helmet_techno' => array(
					'en' => array('34382389','Helmets technology'),
					'fr' => array('34453992','Technologie Casques'),
					'de' => array('34461198','Helm Technologie'),
					'it' => array('34461898','Tecnologia Caschi'),
					'ja' => array('34508758','Helmets technology'),
					'es' => array('34506098','Tecnología Cascos')
				)
			);
			$urlValue = $videoData[$urlValueProb][$lang][0];
			$encTitle = $videoData[$urlValueProb][$lang][1];
			$engTitle = $videoData[$urlValueProb]['en'][1];
		}
?>
<?php 
			if(isset($field_image_encart[0]['filepath']) && !empty($field_image_encart[0]['filepath'])) {
				$posterSrc = base_path().$field_image_encart[0]['filepath'];
				$displayIt = '';// style="display: none;"';
				$ominture =(isset($field_omniture_evar4[0]['value']) && (!empty($field_omniture_evar4[0]['value'])))?(' omniture_click_encart(this, \''. $field_omniture_evar4[0]['value']. '\');'):''; 
				if($userAgent == 'msie7andminus'){
					$playJs = 'vimeoAPI.api_play();';
				}else{
					$playJs = '$f(\'vimeo_'.$urlValue.'\').api(\'play\');';
				}
				$posterOver = '<div class="right_content_poster" alt="'.$encTitle.'" style="background: url(\''.$posterSrc.'\') no-repeat; background-position: 0px 0px;" >'.
				'<div class="right_content_text hover" onclick="'.$ominture.' show_video_encart(); '.$playJs.' ">'.
					'<table width="175" height="50" style="vertical-align: middle; border: none; border-spacing: 0px;"><tbody><tr><td style="line-height: 13px; background: url(\''.base_path().path_to_theme().'/images/vimeo_fleche2.gif\') 25px 17px no-repeat;"><div class="right_content_poster_txt" width="175" height="40" style="padding: 5px 0px 5px 50px; line-height: 13px;">'.
					$encTitle.'</div></td></tr></tbody></table>'.
				'</div>'.
			'</div>';
			} else {
				$posterOver = $displayIt = '';
			}
	?>

<div class="flash_content" id="player_encart"<?php print $displayIt; ?> >
<?php 
	switch($userAgent){
		case 'msie7andminus' :
?>
  <object id="vimeo_<?php echo $urlValue; ?>" type="application/x-shockwave-flash" width="233" height="131" data="http://www.vimeo.com/moogaloop.swf?clip_id=<?php echo $urlValue; ?>&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0">
    <param name="swliveconnect" value="true" />
    <param name="allowscriptaccess" value="always" />
    <param name="quality" value="best" />
    <param name="wmode" value="transparent" />
    <param name="allowfullscreen" value="<?php print ($fullscreen ? 'true' : 'false'); ?>" />
    <param name="scale" value="showAll" />
    <param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=<?php echo $urlValue; ?>&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0" />
	<param name="flashvars" value="js_api=1&clip_id=<?php print $urlValue; ?>&server=vimeo.com&api=1&player_id=vimeo_<?php print $urlValue; ?>" />
  </object>
<?php
		break;
		default:
?>
<iframe id="vimeo_<?php echo $urlValue; ?>" src="http://player.vimeo.com/video/<?php echo $urlValue; ?>?title=0&amp;byline=0&amp;portrait=0&amp;color=FFE500&amp;autoplay=0&amp;api=1&amp;player_id=vimeo_<?php echo $urlValue; ?>" width="233" height="131" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
<?php
		break;
}
?>
</div>
<?php print $posterOver; ?>
<?php 
	switch($userAgent){
		case 'msie7andminus' :
?>
		<script type="text/javascript">
					var GAurl = "<?php print 'play/'.str_replace(" ", "-", $engTitle); ?>",
						mediaName = "<?php print str_replace(" ", "-", $engTitle); ?>",
						mediaLength = 0,
						mediaPlayerName = "VimeoPlayer_encart",
						mediaOffset = 0,
						mediaFirstime = true,
						vimeoAPI = document.getElementById('vimeo_<?php echo $urlValue; ?>');
					s.eVar15 = "<?php print $lang ?>";

		function vimeo_player_loaded(playerid){
			addVimeoEvents(playerid);
		}
		function addVimeoEvents(playerid){
			//vimeoAPI = document.getElementById(""+ playerid + "");
			vimeoAPI.api_addEventListener("play",'vPlayMovie');
			vimeoAPI.api_addEventListener("pause",'vStopMovie');
			vimeoAPI.api_addEventListener("seek",'vSeek');
			vimeoAPI.api_addEventListener("onProgress",'vOnProgress');
			vimeoAPI.api_addEventListener("onLoading",'vOnLoading');
			vimeoAPI.api_addEventListener("onFinish",'endMovie');
		}
		function vPlayMovie(data) {
			if(!mediaFirstime){
				playMovie();
			}
		}
		function vStopMovie() {
            stopMovie();
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vSeek() {
		    stopMovie();
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vOnProgress() {
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vOnLoading() {
			if (mediaFirstime) {
				mediaLength = vimeoAPI.api_getDuration();
				startMovie();
				mediaFirstime = false;
			}       
		}		
					//omniture function
					/*Call on video load*/
					function startMovie(){
						s.Media.open(mediaName,mediaLength,mediaPlayerName);
						s.Media.track(mediaName);
						playMovie();
						//GA
						_gaq.push(['_trackPageview', GAurl]);
					}

					/*Call on video resume from pause and slider release*/
					function playMovie(){
						s.Media.play(mediaName,mediaOffset);
					}

					/*Call on video pause and slider grab*/
					function stopMovie(){
						s.Media.stop(mediaName,mediaOffset);
					}

					/*Call on video end*/
					function endMovie(){
						stopMovie();
						s.Media.close(mediaName);
					}
		</script>
<?php
		break;
		default:
?>

		<script type="text/javascript" src="<?php echo base_path().path_to_theme();?>/js/froogaloop.min.js"></script>
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
					var GAurl = "<?php print 'play/'.str_replace(" ", "-", $engTitle); ?>",
						mediaName = "<?php print str_replace(" ", "-", $engTitle); ?>",
						mediaLength = 0,
						mediaPlayerName = "VimeoPlayer_encart",
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
						//omniture
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
					//omniture function
					/*Call on video load*/
					function startMovie(){	
						s.Media.open(mediaName,mediaLength,mediaPlayerName);
						s.Media.track(mediaName);
						playMovie();
						//GA
						_gaq.push(['_trackPageview', GAurl]);
					}

					/*Call on video resume from pause and slider release*/
					function playMovie(){
						s.Media.play(mediaName,mediaOffset);
					}

					/*Call on video pause and slider grab*/
					function stopMovie(){
						s.Media.stop(mediaName,mediaOffset);
					}

					/*Call on video end*/
					function endMovie(){
						stopMovie();
						s.Media.close(mediaName);
					}
				}
			})();
		</script>
<?php
		break;
}
?>

<?php 
	break;
	case 2 : // news **********************************
		$firsts = array();
		$isCategoryinit = FALSE;
		foreach($menu_news as $data) {
		//print_r($data);
			$firsts[] = reset($data['below']);
			if(isset($field_encart_category) && ($field_encart_category[0]['nid'] == substr($data['link']['href'],5))) {
				$isCategoryinit = TRUE;
				break;
			}
		}
		if($isCategoryinit){
			$first = array_pop($firsts);
		}
		else{
			$sortable_array = array(); 
			foreach ($firsts as $value) { 
				$sortable_array[] = $value[link][weight]; 
			}
			asort($sortable_array);

		  foreach($sortable_array as $k => $v) { 
			  $first = $firsts[$k];
			  break;
		  }
		}
		$link = url($first['link']['href']);
		$title_news_full = $first['link']['title'];
		$title_news = $title_news_full;		
		$item = menu_get_item($first['link']['href']);
		$itemMap = $item['map'][1];
		
		if (!empty($field_omniture_evar4[0]['value'])){
			$tmp = explode('#',$link);
			$tmp[0] .= ((strpos($tmp[0],'?')===FALSE)?'?':'&').'intcmp='.urlencode($field_omniture_evar4[0]['value']);
			$link = implode('#',$tmp);
		}
				
?>
				<a class="link" href="<?php echo $link ?>"<?php if (isset($field_omniture_evar4[0]['value']) && ($field_omniture_evar4[0]['value']!='')) print ' evar4="'.$field_omniture_evar4[0]['value'].'"' ?>>
					<div class="text">
						<p class="helvetica title"><?php print t('news') ?></p>
						<p class="content"><?php echo $title_news; ?></p>
					</div>
					<div style="float:left;background-color:#000;width:117px;height:83px;overflow:hidden;"><table style="width:117px;height:83px;" cellspacing="0" cellpadding="0"><tr><td style="width:117px;height:83px;overflow:hidden;padding: 0 0 0 0;" valign="middle"><img width="117px" src="<?php echo str_replace('.jpg','_m.jpg',$itemMap->field_news_picture_flickr[0]['value'])?>" /></tr></td></table></div>
				</a>
				<?php
	break;
	case 3 : // news gamme **********************************
		$categ_nid = substr($breadcrumb[2]['link']['href'],5);
		$query = 'select distinct n.nid,n.title from content_field_news_family c INNER JOIN node n using (nid) INNER JOIN content_type_news t using (nid)'.
						'where c.field_news_family_nid="'.$categ_nid.'" and n.status=1 '.
						'order by t.field_news_date_value desc LIMIT 0 , 1';
        $res = db_query($query);
		if($news_nid = db_fetch_array($res)) { // only the first
			$news_node = node_load($news_nid['nid']);
			$news_node_full = $news_node->title;
			$title_news = $news_node_full;
			$link = url($news_node->path);
			if (!empty($field_omniture_evar4[0]['value'])){
				$tabtmp = explode('_',$breadcrumb[2]['link']['options']['attributes']['title']);
				$tmp = explode('#',$link);
				$tmp[0] .= ((strpos($tmp[0],'?')===FALSE)?'?':'&').'intcmp='.urlencode($tabtmp[1].' '.$field_omniture_evar4[0]['value']);
				$link = implode('#',$tmp);
			}
?>
			<a class="link" href="<?php echo $link ?>"<?php if (isset($field_omniture_evar4[0]['value']) && ($field_omniture_evar4[0]['value']!='')) print ' evar4="'.$field_omniture_evar4[0]['value'].'"' ?>>
				<div class="text">
					<p class="helvetica title"><?php print t('news') ?></p>
					<p class="content"><?php print $title_news ?></p>
				</div>
				<div style="float:left;background-color:#000;width:117px;height:83px;overflow:hidden;"><table style="width:117px;height:83px;" cellspacing="0" cellpadding="0"><tr><td style="width:117px;height:83px;overflow:hidden;padding: 0 0 0 0;" valign="middle"><img width="117px" src="<?php echo str_replace('.jpg','_m.jpg',$news_node->field_news_picture_flickr[0]['value'])?>" /></tr></td></table></div>
			</a>
<?php
		}
	break;
	default : // normal *****************************
		$link = $field_url_encart[0]['value'];
		if (!empty($field_omniture_evar4[0]['value'])){
			$tmp = explode('#',$link);
			$tmp[0] .= ((strpos($tmp[0],'?')===FALSE)?'?':'&').'intcmp='.urlencode($field_omniture_evar4[0]['value']);
			$link = implode('#',$tmp);
		}
?>
		<a class="link" href="<?php echo $link ?>" target="<?php echo $field_url_target_encart[0]['value'] ?>"<?php if (isset($field_omniture_evar4[0]['value']) && ($field_omniture_evar4[0]['value']!='')) print ' evar4="'.$field_omniture_evar4[0]['value'].'"' ?>>
			<div class="text">
				<p class="helvetica title"><?php print $field_encart_title[0]['value'] ?></p>
				<p class="content"><?php echo $node->content['body']['#value']; ?></p>
			</div>
			<img style="float:left;" src="<?php echo base_path(). $field_image_encart[0]['filepath'] ?>" />
		</a>
<?php
	break;
}
?>

<div class="clear"></div>
