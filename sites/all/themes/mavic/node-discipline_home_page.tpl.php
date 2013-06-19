<?php 
	//if (empty($discpline)) drupal_goto('http://www.mavic.com/'.$lang, NULL, NULL, 301);
?>
	<script type="text/javascript" >
		$(document).ready(function() {
			$("#body-background").ezBgResize();
			checkSize();
		});
	</script>
	<script type="text/javascript">
	var dnlvalue,dfirstFocus=true;
	$(document).ready(function() {
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
	});
	var playersA = new Array();
</script>
<ul id="disc_toolbox">
	<li>
        <a class="button_disc button_facebook" href="http://www.facebook.com/mavic" target="_blank"><span class="helvetica">facebook</span><br /><span class="share_comment"><?php print t('interact with other Mavic fans');?></span></a>
    </li>   
	<li>
		<a class="button_disc button_youtube" href="http://www.youtube.com/user/adminMavic" target="_blank"><span class="helvetica">youtube</span><br /><span class="share_comment"><?php print t('official Mavic video channel');?></span></a>
    </li>   
	<li>
		<a class="button_disc button_twitter" href="http://twitter.com/mavic" target="_blank"><span class="helvetica">twitter</span><br /><span class="share_comment"><?php print t('discover Mavic on twitter');?></span></a>
    </li>   
	<li>
		<form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
			<a href="/<?php print $lang; ?>/newsletter">
				<span class="helvetica">newsletter</span>
			</a>
			<div class="whitebg share_comment">
				<input class="enternl" id="disc_newsl_input" type="text" name="adress" value="<?php print t('Enter your email'); ?>" maxlength="100" autocomplete="off">
				<input class="submitnl" type="submit" name="submitnl" value="OK">
			</div>
		</form>
    </li>   
</ul>
<div id="discipline_content">

	<?php 
		$vSomeSpecialChars = array("'", "\"", "\\", " ", "\(", "\)",",");
		$vReplacementChars = array("_", "_", "_", "_", "_", "_", "_");
		$videodiapo = 0;
		$vout = '';
		$node->field_landing_diapo_bg[0]['value'] != 0 ? $bgcolor='blackbg' : $bgcolor='whitebg';
		$j = 0;
		$omnitureGlobal = $node->field_landing_omiture_global[0]['value'];
		?>
	<div id="landing_diaporama">
		<div id="diapos" class="<?php print $bgcolor;?>">
		<?php 
		$vimeoflash = false;
		foreach($node->field_landing_diaporama as $diaporama)  {
		$j++;
	
		$omniturehiglight = $omnitureGlobal.'_highlights_'.$j;

		if ($diaporama['data']['description'] == 'video') {
				$videoTitle = $node->field_landing_image_video_title;				
				$video = $node->field_landing_video_diapo[$videodiapo];
				$videodiapo++;
				$videoNode = node_load($video['nid']);//video node info
				$videoId = $videoNode->field_vimeo_url[0]['value'];
				$poster = mb_ereg_replace("mini","big",$videoNode->field_video_image[0]['filename']);
				$videodur = $videoNode->field_duration[0]['value'];
				$conf = (!empty($discipline))?$discipline:'';
				$vout = '<div class="diapo_video_block diapo video">';
				if($videoNode->tnid != $videoNode->nid ) {
					$english_node = node_load($videoNode->tnid);
				} else {
					$english_node = $videoNode;
				}
				switch($userAgent){
					case 'msie7andminus' :
						$vimeoflash = true;
						$vout .= '<div style="position:relative;">
							<div class="flash_content" id="player_'.$video['nid'].'">
							  <object id="vimeo_'.$videoId.'" type="application/x-shockwave-flash" width="741" height="418" data="http://www.vimeo.com/moogaloop.swf?clip_id='.$videoId.'&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0">
								<param name="swliveconnect" value="true" />
								<param name="allowscriptaccess" value="always" />
								<param name="quality" value="best" />
								<param name="wmode" value="transparent" />
								<param name="allowfullscreen" value="true" />
								<param name="scale" value="showAll" />
								<param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id='.$videoId.'&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0" />
								<param name="flashvars" value="js_api=1&clip_id='.$videoId.'&server=vimeo.com&api=1&player_id=vimeo_'.$videoId.'" />
							  </object>
							</div>
							<script type="text/javascript" >
								playersA["vimeo_'.$videoId.'"] = new Array();
								playersA["vimeo_'.$videoId.'"]["medianame"] = "'.str_replace(" ", "-", $english_node->title).'";
							</script>
					
				<div class="poster_video">
					<img src="'.base_path().$diaporama['filepath'].'" width="741" height="418" />		
					<a href="javascript:void(0)" onclick="showVideo();document.getElementById(\'vimeo_'.$videoId.'\').api_play();" class="homeplay"></a>
				</div></div>
					</div>';
					break;
					default:
						$vout .= '<div style="position:relative;">
							<div class="flash_content" id="player_'.$video['nid'].'">
							<iframe id="vimeo_'.$videoId.'" src="http://player.vimeo.com/video/'.$videoId.'?title=0&amp;byline=0&amp;portrait=0&amp;color=FFE500&amp;api=1&amp;autoplay=0&amp;player_id=vimeo_'.$videoId.'" width="741" height="418" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
							</div>
							<script type="text/javascript" >
								playersA["vimeo_'.$videoId.'"] = new Array();
								playersA["vimeo_'.$videoId.'"]["medianame"] = "'.str_replace(" ", "-", $english_node->title).'";
							</script>
					
				<div class="poster_video">
					<img src="'.base_path().$diaporama['filepath'].'" width="741" height="418" />		
					<a href="javascript:void(0)" onclick="showVideo();$f(\'vimeo_'.$videoId.'\').api(\'play\')" class="homeplay"></a>
				</div></div>
					</div>';
					break;
				} 
			} else { 
				$url = (strpos($diaporama['data']['description'], '/') === 0) ? $diaporama['data']['description'] : url($diaporama['data']['description']);
				$moreBtn = (strpos($url, 'contest') === FALSE) ? t('more') : t('PARTICIPATE');
				$moreBg = ($diaporama['data']['alt'] == 'white')?' white':' black';
				$vout = '<div class="diapo">
				<img src="'.base_path().$diaporama['filepath'].'" width="741" height="418" />		
				<a href="'.$url.'" onclick="omniture_click(this,'.$omniturehiglight.')" class="homemore'.$moreBg.'">'.$moreBtn.'</a>
			</div>';
			}
			print $vout;
		}?>
		</div>
		<div id="landing_title">
			<div class="nav nav-left"><div id="btn-left"></div></div>
			<div class="nav nav-right"><div id="btn-right"></div></div>
		</div>
	</div>
	<div id="news_content">
		<?php 
	$mtb_news_translat = array(
		"en" => "MTB",
		"fr" => "VTT",
		"de" => "MTB",
		"es" => "MTB",
		"it" => "MTB",
		"ja" => "MTB"
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
	$trad = array();
	$trad['01'] = t('January');
	$trad['02'] = t('February');
	$trad['03'] = t('March');
	$trad['04'] = t('April');
	$trad['05'] = t('May');
	$trad['06'] = t('June');
	$trad['07'] = t('July');
	$trad['08'] = t('August');
	$trad['09'] = t('September');
	$trad['10'] = t('October');
	$trad['11'] = t('November');
	$trad['12'] = t('December');
switch ($discipline) {
	case 'road':
		$a_translat = $road_translat;
		break;
	case 'triathlon':
		$a_translat = $tria_translat;
		break;
	case 'mtb':
		$a_translat = $mtb_news_translat;
		break;
}
			foreach ($menu_news as $news) {
				if ($news['link']['title'] == $a_translat[$lang]){
					$a = 0;
					foreach ($news['below'] as $product ){
						$item = menu_get_item($product['link']['href']);
						$itemMap = $item['map'][1];
						$cleanTitle =   str_replace($vSomeSpecialChars, $vReplacementChars, $itemMap->title);
						$omnitureNews = $omnitureGlobal.'_news_'.$cleanTitle;
						$classe = '';
						$link = url($product['link']['href']);
						$day = (int)substr($itemMap->field_news_date[0]['value'],8,2);
						$month = $trad[substr($itemMap->field_news_date[0]['value'],5,2)];
						$year = substr($itemMap->field_news_date[0]['value'],0,4);
						if ($a == 0){
							$nout = '<div id="news_title" class="disc_title_block">
										<img src="/sites/default/themes/mavic/images/disciplines/disc_news_'.$lang.'.gif" alt="'.t('news').'" />
										<a class="disc_link_bar" href="'.$link.'">'.t('all news').'</a>
									</div>';
						} else {$nout = '';}
						
					?>
						<?php print $nout; ?>
						<div class="news-preview news_item_<?php echo $classe?>" onclick="document.location.href='<?php echo $link?>'; omniture_click(this,'<?php print $omnitureNews ;?>')" >
							<div class="imageslot">
								<img width="240" src="<?php echo str_replace('.jpg','_m.jpg',$itemMap->field_news_picture_flickr[0]['value'])?>" class="imagefield" />
							</div>
							<div class="contentslot">
								<p><b><?php echo $itemMap->title ?></b><br /><?php echo truncate_utf8($itemMap->field_news_intro[0]['value'], 190, true, true);?> <span class="news_date_brother">(<?php echo $day.' '.$month.' '.$year ?></span>)</p>
								<p><img src="/sites/default/themes/mavic/images/more_info.gif" alt=""/>
								<a href="<?php echo $link?>" class="news-more-info">
								<?php echo t('more info');?>
								</a></p>
							</div>
							<div style="clear: both"></div>
						</div>
				
				<?php 
					$a++;
					if ($a == 3) break;
					}
				}
			}
		?>
		
	</div>
	<div id="video_content">
		<div id="video_title" class="disc_title_block">
			<img src="/sites/default/themes/mavic/images/disciplines/disc_videos_<?php print $lang; $vmenu = @reset($menu_video);?>.gif" alt="<?php print t('video');?>" />
			<a class="disc_link_bar" href="<?php print url($vmenu['link']['href']);?>"><?php print t('all videos');?></a>
		</div>
		<?php 
			foreach($node->field_landing_video as $video){
					$videoNode = node_load($video['nid']);//video node info
					$videoDesc = $videoNode->field_vimeo_url[0]['data']['description'];
					$poster = mb_ereg_replace("mini","big",$videoNode->field_video_image[0]['filename']);
					$videodur = $videoNode->field_duration[0]['value'];
					$videoTitle = $videoNode->title;
					if($videoNode->tnid != $videoNode->nid ) {
						$english_node = node_load($videoNode->tnid);
					} else {
						$english_node = $videoNode;
					}


		?>
		<div class="video_block">
				<div class="video_left">
					<p class="videodesc">
						<b><?php print $videoTitle; ?></b><br />
						<?php print $videoDesc; ?>
					</p>
					<p class="videodur">
						<b><?php echo t('duration');?>&nbsp;:</b> <?php echo $videodur ;?>	
					</p>
				</div>
					<?php if ($userAgent != 'msie7andminus'){	//var_dump($videoNode->field_vimeo_url[0]['data']['thumbnail_large']);	die;		?>
					<div class="flash_content" id="player_<?php print $video['nid']; ?>">
						<div class="smallvideo" id="vThumb_<?php print $video['nid']; ?>" >
							<img src="<?php print $videoNode->field_vimeo_url[0]['data']['thumbnail_large']; ?>" width="494" height="278" />		
							<a href="javascript:void(0)" onclick="showSmallVideo('<?php print $video['nid']; ?>');$f('vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>').api('play');" class="homeplay"></a>
						</div>			
						<iframe id="vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>" src="http://player.vimeo.com/video/<?php echo $videoNode->field_vimeo_url[0]['value']; ?>?title=0&amp;js_api=1&amp;api=1&amp;byline=0&amp;portrait=0&amp;color=FFE500&amp;player_id=vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>&amp;autoplay=0&amp;api=1" width="494" height="278" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
					</div>
<?php 						} else {
?>
					<div class="flash_content" id="player_<?php print $video['nid']; ?>">
						<div class="smallvideo" id="vThumb_<?php print $video['nid']; ?>" >
							<img src="<?php print $videoNode->field_vimeo_url[0]['data']['thumbnail_large']; ?>" width="494" height="278" />		
							<a href="javascript:void(0)" onclick="showSmallVideo('<?php print $video['nid']; ?>'); playIt('vimeo_<?php print $videoNode->field_vimeo_url[0]['value']; ?>');" class="homeplay"></a>
						</div>
						  <object id="vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>" type="application/x-shockwave-flash" width="494" height="278" data="http://www.vimeo.com/moogaloop.swf?clip_id=<?php echo $videoNode->field_vimeo_url[0]['value']; ?>&amp;js_api=1&amp;api=1&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0">
							<param name="swliveconnect" value="true" />
							<param name="allowscriptaccess" value="always" />
							<param name="quality" value="best" />
							<param name="wmode" value="transparent" />
							<param name="allowfullscreen" value="true" />
							<param name="scale" value="showAll" />
							<param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=<?php echo $videoNode->field_vimeo_url[0]['value']; ?>&server=www.vimeo.com&fullscreen=1&show_title=0&show_byline=0&show_portrait=0&color=FFE500&autoplay=0" />
							<param name="flashvars" value="js_api=1&clip_id=<?php print $videoNode->field_vimeo_url[0]['value']; ?>&server=vimeo.com&api=1&player_id=vimeo_<?php print $videoNode->field_vimeo_url[0]['value']; ?>" />
						  </object>
					</div>
				<?php  	} ?>
					<script type="text/javascript" >
						playersA["vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>"] = new Array();
						playersA["vimeo_<?php echo $videoNode->field_vimeo_url[0]['value']; ?>"]['medianame'] = "<?php echo str_replace(" ", "-", $english_node->title); ?>";
					
					</script>

				<div class="clear"></div>
		</div><!--end video -->
		<?php 
		}
		?>
	</div><div class="clear"></div><!-- end content-video -->
	<div><!--content-techno -->
		<div id="content_title" class="disc_title_block">
			<?php 
					$menuT = @reset($menu_technologies);
					if(!empty($menuT['below']))
					{
						$menuT = reset($menuT['below']);
					}
			?>
			<img src="/sites/default/themes/mavic/images/disciplines/disc_techno_<?php print $lang; ?>.gif" alt="<?php print t('technologies');?>" />
			<a class="disc_link_bar" href="<?php print url($menuT['link']['href']);?>"><?php print t('all technologies');?></a>
		</div>
		<?php
			$t=0;	
			foreach($node->field_technologienode as $techno){ 
				$technoNode = node_load($techno['nid']);//node techno
				$technoDesc = $technoNode->body;
				$technoHref = $technoNode->path;
				$technoTitle = $technoNode->title;
				$technoImg = base_path().$node->field_landing_image_techno[$t]['filepath'];
				$omniture = $omnitureGlobal.'_techno_'.$node->field_landing_omniture[$t]['value'];
		?>
	<div class="techno_block">
		<div class="img_contain">
			<img class="techno_img" src="<?php print $technoImg; ?>" />
		</div>
		<div class="techno_txt">
			<p class="techno_title">
				<b><?php print $technoTitle; ?></b>
			</p>
			<p class="techno_desc">
				<?php print $technoDesc; ?>
			</p>
			<p class="more">
				<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture;?>" onclick="omniture_click(this,'<?php print $omniture?>')">
					<img border="0" src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
				</a>
				<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture;?>" onclick="omniture_click(this,'<?php print $omniture?>')">
					<?php print t('More info'); ?>
				</a>
			</p>
		</div>
	</div><div class="clear"></div>
		<?php	
			$t++;
			}
			?>
	</div><!--end techno -->
	<div id="event_content"><!-- content-event -->
		<div id="event_title" class="disc_title_block">
			<img src="/sites/default/themes/mavic/images/disciplines/disc_event_<?php print $lang; $menuAs = end($menu_assistance);?>.gif" alt="<?php print t('events');?>" />
			<a class="disc_link_bar" href="<?php print url($menuAs['link']['href']);?>"><?php print t('all events');?></a>
		</div>
	<?php
		$n= 0;
		foreach ($field_disc_event_date as $event){
			$eventDate = $event['value'];
			$eventTitle = $locations[$n]['name'];
			$eventCity = $locations[$n]['city'];
			$eventCountry = $locations[$n]['country_name'];
			$n++;
			?>
			<div class ="event_block event_<?php print $n; ?>">
				<p class="txt_contain">
					<span><b><?php print $eventTitle; ?></b></span><br />
					<span><?php print $eventDate; ?></span><br />
					<span><?php print $eventCity; ?></span><br />
					<span><?php print $eventCountry; ?></span>
				</p>
			</div>
			<?php
		}
	?>
	</div><!-- end content-event -->
	<?php if (!$vimeoflash){ ?>
		<script type="text/javascript" src="<?php echo base_path().path_to_theme();?>/js/froogaloop.min.js"></script>
		<script type="text/javascript">
            (function(){
                // Listen for the ready event for any vimeo video players on the page
                var vimeoPlayers = document.querySelectorAll('iframe'),
                    player;
                for (var i = 0, length = vimeoPlayers.length; i < length; i++) {
                    player = vimeoPlayers[i];
                    $f(player).addEvent('ready', ready);
                }

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
					var GAurl = "play/" + playersA[player_id]['medianame'],
						mediaName = playersA[player_id]['medianame'],
						mediaLength = 0,
						mediaPlayerName = "VimeoPlayer_discipline_homepage",
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

	<?php } else { ?>
		<script type="text/javascript">
		function playIt(playerId){
		var vimeo_obj = document.getElementById(playerId); 
		vimeo_obj.api_play();
		}
				s.eVar15 = "<?php print $lang ?>";
		function vimeo_player_loaded(playerid){
			var playerid = playerid.toString();
				playersA[playerid]["GAurl"] = "play/" + playersA[playerid]["medianame"],
				playersA[playerid]["mediaLength"] = 0,
				playersA[playerid]["mediaPlayerName"] = "VimeoPlayer_discipline_homepage",
				playersA[playerid]["mediaOffset"] = 0,
				playersA[playerid]["mediaFirstime"] = true;

			var vimeoAPI = document.getElementById(playerid);
				vimeoAPI.api_addEventListener("play","vPlayMovie");
				vimeoAPI.api_addEventListener("pause","vStopMovie");
				vimeoAPI.api_addEventListener("seek","vSeek");
				vimeoAPI.api_addEventListener("onProgress","vOnProgress");
				vimeoAPI.api_addEventListener("onLoading","OnLoading");
				vimeoAPI.api_addEventListener("onFinish","endMovie");
		}
			
			function vPlayMovie(playerid) {
				var sdfsdf = playersA[playerid]["mediaFirstime"];
				if(!sdfsdf){
					playMovie(playerid);
				}
			}
			function vStopMovie(playerid) {
				stopMovie(playerid);	
				playersA[playerid]["mediaOffset"] = document.getElementById(playerid).api_getCurrentTime();
			}
			function vSeek(playerid) {
				stopMovie(playerid);
				playersA[playerid]["mediaOffset"] = document.getElementById(playerid).api_getCurrentTime();
			}
			function vOnProgress(playerid) {
				playersA[playerid]["mediaOffset"] = document.getElementById(playerid).api_getCurrentTime();
			}
			function vOnLoading(playerid) {
			var sdfsdf=playersA[playerid]["mediaFirstime"];
			if (sdfsdf) {
					playersA[playerid]["mediaLength"] = document.getElementById(playerid).api_getDuration();
					startMovie(playerid);
					playersA[playerid]["mediaFirstime"] = false;
					
				}
			}		
											//omniture function
						/*Call on video load*/
						function startMovie(playerid){
						alert(playerid);
							s.Media.open(playersA[player_id]["medianame"],playersA[playerid]["mediaLength"],playersA[playerid]["mediaPlayerName"]);
							s.Media.track(playersA[player_id]["medianame"]);
							playMovie(playerid);
							//GA
							_gaq.push(["_trackPageview", playersA[playerid]["GAurl"]]);
							
						}

						/*Call on video resume from pause and slider release*/
						function playMovie(playerid){
							s.Media.play(playersA[player_id]["medianame"],playersA[playerid]["mediaOffset"]);
						}

						/*Call on video pause and slider grab*/
						function stopMovie(playerid){
							s.Media.stop(playersA[player_id]["medianame"],playersA[playerid]["mediaOffset"]);
						}

						/*Call on video end*/
						function endMovie(playerid){
							stopMovie(playerid);
							s.Media.close(playersA[player_id]["medianame"]);
						}
						


		
		</script>
	<?php } ?>
</div>
