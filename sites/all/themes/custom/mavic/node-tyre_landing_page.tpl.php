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
</script>
<!-- Sharing box top right -->
<ul id="disc_toolbox">
	<li>
        <a class="button_disc button_facebook" href="http://www.facebook.com/mavic" target="_blank"><span class="helvetica">facebook</span><br /><span class="share_comment"><?php print t('interact with other Mavic fans');?></span></a>
    </li>     
	<li>
		<a class="button_disc button_catalog" href="<?php $catalog = reset($menu_download); print url($catalog['link']['href']);?>?intcmp=landingpage_tyre12_download" onclick="omniture_click(this, 'landingpage_tyre12_download');" target="_blank"><span class="helvetica"><?php print t('catalog') ?></span><br /><span class="share_comment"><?php print t('download the 2012 catalog');?></span></a>
    </li> 	
	<li>
		<form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
			<a href="/<?php print $lang; ?>/newsletter?intcmp=landingpage_tyre12_newsletter" onclick="omniture_click(this, 'landingpage_tyre12_newsletter');">
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
<!-- Main Content -->
<div>
	<?php 
//var_dump($node);die;
	function replace_special_char($nom){
		return str_replace( array('/',' ','à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('_','_','a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $nom); 
	}
	//
	// to have english version for omniture
	//
	if($tnid != $nid) {
		$english_node = node_load($tnid);
	} else {
		$english_node = $node;
	}
	$videoId = 37513349;//$field_vimeo_url[0]['data']['id'];
	
	?>
	<!-- Video $f('vimeo_<?php print $videoId; ?>').api('play')-->
	<?php ?>
	<div id="video_tyre">
		<div class="flash_content" id="player_<?php print $nid; ?>">
			<iframe id="vimeo_<?php print $videoId; ?>" src="http://player.vimeo.com/video/<?php print $videoId; ?>?title=0&amp;byline=0&amp;portrait=0&amp;color=FFE500&amp;api=1&amp;autoplay=0&amp;player_id=vimeo_<?php print $videoId; ?>" width="741" height="418" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		</div>
		<div class="poster_video">
			<img src="<?php print base_path().path_to_theme().'/images/landingpage/tyres2012/video_thumb_'.$lang.'.jpg'; ?>" width="741" height="418" />		
			<a href="javascript:void(0)" onclick="showVideo();" class="homeplay"></a>
		</div>
	</div>
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
					var GAurl = "<?php print 'play/'.str_replace(" ", "-", $english_node->field_vimeo_url[0]['data']['title']); ?>",
						mediaName = "<?php print str_replace(" ", "-", $english_node->field_vimeo_url[0]['data']['title']); ?>",
						mediaLength = 0,
						mediaPlayerName = "Tyre_landing_page",
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
	<!-- end Video -->
		<img class="tyre-titles" src="/sites/default/themes/mavic/images/landingpage/tyres2012/titre1_<?php echo $lang;?>.gif" alt="R&D Process">
	<!-- Diaporama -->
		<script type="text/javascript" src="<?php echo base_path().path_to_theme();?>/js/lightbox.js"></script>
	<script type="text/javascript">
	    $(function() {
        $('#grid a').lightBox();
    });

	</script>
	<div id="tyre_landing_diaporama">
		<div id="grid">
		<?php 
		$i=0;
		foreach($field_landing_diapo_titre as $altDiapo){
			$titleDiapo = $altDiapo['value'];
			$descDiapo = $field_landing_diapo_desc[$i]['value'];
			((($i+1)%5) == 0)?$lastOne=' lastone':$lastOne='';
		?>
			<a  class="feature-image-notooltip<?php print $lastOne; ?>" title="<?php print $titleDiapo; ?>" desc="<?php print $descDiapo; ?>" href="<?php print base_path().path_to_theme().'/images/landingpage/tyres2012/imgx/i'.($i + 1).'.jpg?v=1'; ?>"><img src="<?php print base_path().path_to_theme().'/images/landingpage/tyres2012/thumbx/t'.($i + 1).'.jpg'; ?>" /></a>
		<?php 
			
		$i++;
		} 
		?>
		</div>
		<div class="clear"></div>
		
	</div>
	<!-- End Diaporama -->
	<!-- Technologies -->
	<div id="tyres_technos">
	<img class="tyre-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/tyres2012/titre2_<?php echo $lang;?>.gif" width="741" />
	<p>
	<?php print $node->content['body']['#value']; ?>
	</p>
			
		<?php
			$t=0;
			foreach($field_landing_image_techno as $ergoride){
				$technoNode = node_load($field_technologienode[$t]['nid']);//node techno
				$technoHref = $technoNode->path;
				$technoSubtitle = $field_landing_techno_subtitle[$t]['value'];
				$technoDesc = htmlspecialchars_decode($field_landing_techno_desc[$t]['value']);
				$technoImg = base_path().$ergoride['filepath'];
				//
				// to have english version for omniture
				//
				if($technoNode->tnid != $technoNode->nid) {
					$english_node = node_load($technoNode->tnid);
				} else {
					$english_node = $technoNode;
				}
				$omniture = 'landingpage_tyre12_'.replace_special_char($english_node->title);
		?>
	<div class="techno_block">
		<img class="techno_img" src="<?php print $technoImg; ?>" />
		<div class="techno_bloc_right">
			<img class="techno_title" border="0" src="/sites/default/themes/mavic/images/landingpage/tyres2012/titre_techno_<?php echo $t;?>.gif" />
			<p class="techno_subtitle">
				<b><?php print $technoSubtitle; ?></b><br />
		
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
			$i=0;
			//var_dump($primary_links); die;
			reset($primary_links);
			foreach ( $primary_links as $range){
				if ($i == 0){
					$hrefAA = next($range['below']);
					$hrefA = current($hrefAA['below']);
					$wheelStHref = url($hrefA['link']['href']);
				}
				if ($i == 2){
					$hrefAA = current($range['below']);
					$hrefA = current($hrefAA['below']);
					$tyresHref = url($hrefA['link']['href']);
					break;
				}
				$i++;
			}
			$pfArray = array(
				'en' => 251193,
				'fr' => 251196,
				'de' => 251199,
				'it' => 251202,
				'ja' => 251208,
				'es' => 251205
			);
			?>
				</div>
	<!--end techno -->
	<!-- Discover -->
	<div id="discover_range">
		<img class="tyre-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/tyres2012/titre3_<?php echo $lang;?>.gif" width="741" />
		<a class="tyres_rollover" href="<?php echo $tyresHref; ?>?intcmp=landingpage_tyre12_tyre_range" onclick="omniture_click(this, 'landingpage_tyre12_road_tyre');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/tyres2012/disco1_<?php echo $lang; ?>.jpg');"></a>
		<a class="wheelsTS" href="<?php echo $wheelStHref; ?>?intcmp=landingpage_tyre12_wheel_tyre_syst_range&amp;pf=<?php print $pfArray[$lang]; ?>" onclick="omniture_click(this, 'landingpage_tyre12_main_tyre');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/tyres2012/disco2_<?php echo $lang; ?>.jpg');"></a>
	</div>
	<!-- End discover -->
	<!-- Subscribe -->
	<div id="nl_subscribe">
		<img class="tyre-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre4_<?php echo $lang;?>.gif" width="741" />
		<a class="tyres_nlsubcsribe" href="<?php echo url('newsletter',array('absolute'=>TRUE)); ?>?intcmp=landingpage_tyre12_newsletter" onclick="omniture_click(this, 'landingpage_tyre12_newsletter');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/tyres2012/signup_<?php echo $lang; ?>.gif');"></a>
	</div>
	<!-- End subscribe -->	
</div>
<!-- End main content -->

