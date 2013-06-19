

<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		var hauteurDesc = $("#infosVideo").height() - $("#MoreInfosVideo").height();
		if ($("#ivContainer").length > 0){
			var infoV = $("#ivContainer").css("height","32px");
		} else {
			var infoV = $("#infosVideo").css("height","32px");
		}
		$("#MoreInfosVideo").click(function(){
				infoV.animate({"height": hauteurDesc}, "slow");
				$(this).css("visibility","hidden");
		});
		$(".video_item").hover(function(){
			$(this).css({backgroundPosition:"0 -100px",backgroundColor:"#ffe500"});
		},
		function(){
			$(this).css({backgroundPosition:"0 0",backgroundColor:"transparent"});
		}).click(function(){
			location.href=$("a",this).attr("href");
		});
	});
</script>
<!-- video -->

<div id="video_page">
<?php if(!isset($field_vimeo_url) || empty($field_vimeo_url)){?>
	<div>
		<?php echo $node->content['body']['#value']; ?>
	</div>
<?php } else { ?>
	<div id="playerMavicVideos" ><?php  print $content;?></div>
			<?php			
			//
			// to have english version for omniture
			//
			if($tnid != $nid) {
				$english_node = node_load($tnid);
			} else {
				$english_node = $node;
			}
		?>
			<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox video">
					<div class="custom_images">
						<a class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
						<a class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" alt="<?php print t('Share to Twitter'); ?>" />
						</a>
						<a class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" alt="<?php print t('Share to Facebook'); ?>" />
						</a>
					</div>
				</div>
			<!-- AddThis Button END -->
	<div id="conteneurMavicVideo">
	<?php //if($field_vimeo_url[0]['title'] == 'english') {
		if(!empty($field_vimeo_description[0]['value'])) {
		$vTitle = $title;
		$vDesc = $field_vimeo_description[0]['value'];
	} else {
		$vTitle = $field_vimeo_url[0]['data']['title'];
		$vDesc = $field_vimeo_url[0]['data']['description'];
	};
	switch($userAgent){
		case 'msie7andminus' :
	?>
		<script type="text/javascript">
		var GAurl = "<?php print 'play/'.str_replace(" ", "-", $english_node->title); ?>",
			mediaName = "<?php print str_replace(" ", "-", $english_node->title); ?>",
			mediaLength = 0,
			mediaPlayerName = "VimeoPlayer_page",
			mediaOffset = 0,
			mediaFirstime = true,
			theplayerid = "";
			s.eVar15 = "<?php print $lang ?>";

		function vimeo_player_loaded(playerid){
			theplayerid = playerid
			addVimeoEvents(theplayerid);
		}
		function addVimeoEvents(playerid){
			var vimeoAPI = document.getElementById(""+ playerid+ "");
			vimeoAPI.api_addEventListener("play",'vPlayMovie');
			vimeoAPI.api_addEventListener("pause",'vStopMovie');
			vimeoAPI.api_addEventListener("seek",'vSeek');
			vimeoAPI.api_addEventListener("onProgress",'vOnProgress');
			vimeoAPI.api_addEventListener("onLoading",'vOnLoading');
			vimeoAPI.api_addEventListener("onFinish",'endMovie');
		}
		function vPlayMovie(data) {
		console.log('play');
			if(!mediaFirstime){
				playMovie();
			}
		}
		function vStopMovie() {
            stopMovie();
			var vimeoAPI = document.getElementById(""+ theplayerid+ "");
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vSeek() {
		    stopMovie();
			var vimeoAPI = document.getElementById(""+ theplayerid+ "");
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vOnProgress() {
			var vimeoAPI = document.getElementById(""+ theplayerid+ "");
			mediaOffset = vimeoAPI.api_getCurrentTime();
		}
		function vOnLoading() {
			if (mediaFirstime) {
				var vimeoAPI = document.getElementById(""+ theplayerid+ "");
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
<?php	break;
		default :
?>
		<script type="text/javascript" src="<?php echo base_path().path_to_theme();?>/js/froogaloop.min.js"></script>
		<script type="text/javascript">
            (function(){
                // Listen for the ready event for any vimeo video players on the page
                var player = document.querySelectorAll('iframe');
                    $f(player[0]).addEvent('ready', ready);
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
					var GAurl = "<?php print 'play/'.str_replace(" ", "-", $english_node->title); ?>",
						mediaName = "<?php print str_replace(" ", "-", $english_node->title); ?>",
						mediaLength = 0,
						mediaPlayerName = "VimeoPlayer_page",
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

		<h1 class="helvetica titreMavicVideo"><?php  print $vTitle;?></h1>

		<div id="ivContainer" style="overflow:hidden;">
			<div id="infosVideo">
			<?php  print $vDesc;?><br /><br />
			</div>
		</div>	
	</div>
	<a href="#" id="MoreInfosVideo" style="padding-left:13px;">More infos</a>
<?php } ?>
	<div class="duration">
		<b><?php echo t('duration');?></b> <?php echo $node->field_duration[0]['value'];?>
	</div>
</div>

<div class="view-video-page">
	<?php

	$i = 0;
	foreach($menu_video as $video) {
		
		$item = menu_get_item($video['link']['href']);
		$itemMap = $item['map'][1];
		$displayIt = false;
		if(!empty($discipline)){
			foreach($itemMap->field_video_discipline as $videoDisc){
				if($videoDisc['value'] == $discipline) $displayIt = true;
			}
		} else {
			$displayIt = true;
		}
		if($displayIt){
			$i++;
			$link = url($video['link']['href']);
			if($video['link']['in_active_trail'])
			{
				$link = '#';
				$classe = '_active'; 
			}
			else $classe = '';
?>
		<div class="video_item<?php echo $classe ?> odd<?php echo $i%2?>" style="background-image:url(<?php echo base_path().$itemMap->field_video_image[0]['filepath'] ; ?>)">
				<span class="block">
					<span class="title"><b><a href="<?php echo $link?>"><?php echo $itemMap->title ?></a></b><br/>
					<?php echo $itemMap->field_video_date[0]['value'] ?><br/>
					</span>
					<span class="duration">
						<b><?php echo t('duration');?>:</b> <?php echo $itemMap->field_duration[0]['value'];?>	
					</span>
				</span>
		</div>
<?php
		}
	}
?>

<div class="clear"></div>
</div>




