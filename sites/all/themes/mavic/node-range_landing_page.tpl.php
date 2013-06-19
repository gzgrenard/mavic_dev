	<script type="text/javascript" >
		$(document).ready(function() {
			$("#body-background").ezBgResize();
			checkSize();
                        $('#discover_range_helmet a').hover(function(){
                            $(this).css('background-position-y', '-230px');
                        },function(){
                            $(this).css('background-position-y', '0px');
                        })
		});
	</script>
        <?php
        $omnitureGlobal = $node->field_landing_omiture_global[0]['value'];
	$omnitureDownload = $omnitureGlobal.'download';
	$omnitureDisco = $omnitureGlobal.'discoverrange';
        $helmetsNid = array('252873','252875','252878','252881','252883','252884');
        if (in_array($node->nid, $helmetsNid)){
        ?>
        <!-- Sharing box top right -->
<ul id="disc_toolbox">
		<li>
				<div class="share">
						<span class="helvetica"><?php print t('share');?></span>
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
										templates: { twitter: '<?php print $title; ?> {{url}} #bike #helmets @mavic' }
								};
								
						</script>
						<!-- AddThis Button END -->
				</div>
		</li>     
		<li>
				<a class="button_disc button_catalog" href="<?php $catalog = reset($menu_download); print url($catalog['link']['href']);?>?intcmp=<?php print $omnitureDownload ?>" onclick="omniture_click(this, '<?php print $omnitureDownload ?>');" target="_blank"><span class="helvetica"><?php print t('catalog') ?></span><br /><span class="share_comment"><?php print t('download the 2013 catalog');?></span></a>
				</li> 	
		<li>
		<form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
			<a href="/<?php print $lang; ?>/newsletter?intcmp=<?php echo $omnitureGlobal;?>_newsletter" onclick="omniture_click(this, '<?php echo $omnitureGlobal;?>_newsletter');">
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
<?php }?>
<div>
	<?php $node->field_landing_diapo_bg[0]['value'] != 0 ? $bgcolor='blackbg' : $bgcolor='whitebg'?>
	<div id="landing_diaporama">
		<div id="diapos" class="<?php print $bgcolor;?>">
		<?php foreach($node->field_landing_diaporama as $diaporama):?>
			<img class="diapo" src="<?php print base_path().$diaporama['filepath']; ?>" width="741" height="556" />		
		<?php endforeach ?>
		</div>
		<div id="landing_title">
			<div class="nav nav-left"><div id="btn-left"><!-- --></div></div>
			<div class="nav nav-right"><div id="btn-right"><!-- --></div></div>
			<img src="<?php print base_path().$node->field_landing_image_page_title[0]['filepath']; ?>" />
		</div>
	</div>
    <div id="discipline_content">	
        <div id="video_content">
		<?php 
			$i=0;
			$videoEncart = $node->field_landing_video_encart;
			$videoTitle = $node->field_landing_image_video_title;
			$videoDesc = $node->field_landing_video_description;
			
			foreach($node->field_landing_video as $video){
				$encart = node_load($videoEncart[$i]['nid']);//encart node info
				$url = $encart->field_url_encart;
				$videoId = $url[0]['value'];
				$videoNode = node_load($video['nid']);//video node info
				$poster = mb_ereg_replace("mini","big",$videoNode->field_video_image[0]['filename']);
				$videodur = $videoNode->field_duration[0]['value'];
		?>
		<div class="video_block">
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
                                        <div class="video_right">
					<img src="<?php print base_path().$videoTitle[$i]['filepath']; ?>" />
					<p class="videodesc">
						<?php print $videoDesc[$i]['value']; ?>
					</p>
					<p class="videodur">
						<b><?php echo t('duration');?>:</b> <?php echo $videodur ;?>	
					</p></div><div class="clear"></div>
		 </div>		
		<?php 
			$i++;
			} 
		?>
                <script type="text/javascript" src="<?php echo base_path().path_to_theme();?>/js/froogaloop.min.js"></script>
               
        </div>
    </div><div class="clear"></div><!-- end content-video -->
		<?php
			$t=0;	
			foreach($node->field_technologienode as $techno){ 
				$technoNode = node_load($techno['nid']);//node techno
				$technoDesc = $technoNode->body;
				$technoHref = $technoNode->path;
				$technoTitle = base_path().$node->field_landing_image_techno_title[$t]['filepath'];
				$technoSubtitle = $node->field_landing_techno_subtitle[$t]['value'];
				$technoImg = base_path().$node->field_landing_image_techno[$t]['filepath'];
				$omniture = $omnitureGlobal.$node->field_landing_omniture[$t]['value'];
		?>
	<div class="techno_block">
		<img class="techno_img" src="<?php print $technoImg; ?>" />
		<img class="techno_title" src="<?php print $technoTitle; ?>" />
		<p class="techno_subtitle">
			<b><?php if (!in_array($nid, $helmetsNid)) print $technoSubtitle; ?></b>
		</p>
                <div class="techno_desc techno_desc_lp_m">
		<p class="techno_desc_lp">
			<?php print trim($technoDesc); ?>
		</p>
                   <!-- <p class="techno_conso">
                <?php if(!empty($technoNode->field_consoarglb[0]['value'])) { ?>

				
					<?php
						foreach ($technoNode->field_consoarglb as $conso) {
							echo '- '.$conso['value'].'<br />';
						}
					?>
				
			<?php } ?>
                    </p> -->
                </div>
		<p class="more">
			<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture;?>" onclick="omniture_click(this,'<?php print $omniture?>')">
				<img border="0" src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
			</a>
			<a href="<?php print $technoHref; ?>?intcmp=<?php print $omniture;?>" onclick="omniture_click(this,'<?php print $omniture?>')">
				<?php print t('More info'); ?>
			</a>
		</p>
	</div><div class="clear"></div><!--end techno -->
		<?php	
			$t++;
			}
                        //the footer changes for the helmets range
                        
                        
                        
                        if (!in_array($nid, $helmetsNid)){

			$catalog = reset($menu_download);
			?>
	<div>
		<a class="cat_down" href="<?php print url($catalog['link']['href']); ?>?trcmp=<?php print $omnitureDownload ?>" onclick="omniture_click(this,'<?php print $omnitureDownload ?>');">
			<img border="0" src="/sites/default/themes/mavic/images/landingpage/cat_down_title_<?php print $lang; ?>.gif" alt="<?php t('catalog download'); ?>" width="242" height="139" />
		</a>
		<a href="<?php print $node->body; ?>?trcmp=<?php print $omnitureDisco ?>">
		<div id="discover" onclick="discover_range();omniture_click(this,'<?php print $omnitureDisco ?>');">
			<div id="disco_title" style="background:url(<?php print base_path().$node->field_landing_image_disco_title[0]['filepath']; ?>) no-repeat;"></div>
			<div id="disco_bc" style="background:url(<?php print base_path().$node->field_landing_image_disco[0]['filepath']; ?>) no-repeat;"></div>
		</div>
		</a>
	</div>
                <?php
                        } else { ?>
				<!-- Discover -->
				<div id="cxr_discover" class="anchor_target">
						<div id="discover_range_helmet" class>
								<img class="cxr-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/tyres2012/titre3_<?php echo $lang;?>.gif" width="741" />
								<a href="<?php print $node->body; ?>?trcmp=<?php print $omnitureDisco ?>" onclick="omniture_click(this, '<?php print $omnitureDisco ?>');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/helmets/helmet_range_<?php echo $lang;?>.jpg'); width:100%;"></a>
						</div>
				</div>
				<!-- End discover -->
				<!-- Subscribe -->
				<div id="cxr_suscribe">
						<div id="nl_subscribe">
								<img class="cxr-titles" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre4_<?php echo $lang;?>.gif" width="741" />
								<a class="cxr_nlsubcsribe" href="<?php echo url('newsletter',array('absolute'=>TRUE)); ?>?intcmp=<?php echo $omnitureGlobal;?>_newsletter" onclick="omniture_click(this, '<?php echo $omnitureGlobal;?>_newsletter');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/tyres2012/signup_<?php echo $lang; ?>.gif');"></a>
						</div>
				</div>
				<!-- End subscribe -->
                <?php   
                        }
                       ?>
</div>