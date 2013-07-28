	<script type="text/javascript" >
             if(language != 'ja') {
                            Cufon.replace(".ss2012_discover", {hover: true, "font-family": "Helvetica65-Medium"});
                        }
		$(document).ready(function() {
			$("#body-background").ezBgResize();
			checkSize();
                       
                        $(".ss2012_discover").each(function(i){
                            $(this).data("contentxt", $(this).html()).data("bckgr",$(this).css("background-image")).html("").hover(
                                function(){
                                    $(this).css({"background-image":"none","background-color":"#FFE500"}).html($(this).data("contentxt"));
                                },
                                function(){
                                    $(this).css({"background-image":$(this).data("bckgr"),"background-color":"transparent"}).html("");
                                }
                            );               
                        });
		});
	</script>
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
										templates: { twitter: '<?php print $title; ?> {{url}} #bike @mavic' }
								};
								
						</script>
						<!-- AddThis Button END -->
				</div>
		</li>     
		<li>
				<a class="button_disc button_catalog" href="<?php $catalog = reset($menu_download); print url($catalog['link']['href']);?>?intcmp=<?php print $omnitureDownload ?>" onclick="omniture_click(this, '<?php print $omnitureDownload ?>');" target="_blank"><span class="helvetica"><?php print t('catalog') ?></span><br /><span class="share_comment"><?php print t('download the FW 2012 catalog');?></span></a>
				</li> 	
		<li>
		<form class="button_disc button_newsletter" id="disc_nlsubmit" action="/<?php print $lang; ?>/newsletter/" method="post">
			<a href="/<?php print $lang; ?>/newsletter?intcmp=landingpage_FWapparel12_newsletter" onclick="omniture_click(this, 'landingpage_FWapparel12_newsletter');">
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

<div>
	<?php 
	$roadLinkActive = '';
	$mtbLinkActive = '';
	$mindia = -1;
	$maxdia = 4;

        function replace_special_char($nom){
		return str_replace( array('/',' ','à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('_','_','a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $nom); 
	}

	?>
	<div id="ss2012_landing_header">
		<img class="ss2012title" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre1_<?php echo $lang;?>.gif" width="741" />
	</div>
	<div id="ss2012_landing_diaporama">
		<div id="diapos" class="whitebg">
		<?php 
		$i=0;
                $itemNmb = array(6,5,5);
                $total = 0;
		foreach($field_ss2012_slide_img as $diaporama){
                
		?>
		<div class="diapo">
			<img class="" src="<?php print base_path().$diaporama['filepath']; ?>" width="371" height="684" />
			<div class="diapo_col_right">
			<?php 
			for ($k = 0; $k < $itemNmb[$i]; $k++) {

                                $l = $total + $k; // 0 6 11
				$macro_title = $field_ss2012_macro_data[$l]['safe']['title'];
				$macro_node = node_load($field_ss2012_macro_data[$l]['nid']);
				//
				// to have english version for omniture
				//
				if($macro_node->tnid != $macro_node->nid) {
					$english_node = node_load($macro_node->tnid);
				} else {
					$english_node = $macro_node;
				}
				$macro_omniture = 'landingpage_FWapparel12_'.replace_special_char($english_node->title);
				$macro_desc = $macro_node->field_usp[0]['value'];
				$macro_link = url($macro_node->path);
			?>
				<div class="macro_bloc<?php if ($k == 0) echo ' firstone'; if ($i > 0) echo ' onlyfive'; ?>">
					<img class="macro_img" src="<?php print base_path().$field_ss2012_macro_img[$l]['filepath']; ?>" width="113" height="113" />				
					<div class="macro_bloc_right">
						<div class="title"><?php echo $macro_title; ?></div>
						<p><?php echo $macro_desc; ?></p>
						
						<p class="more">
			<a class="more" onclick="omniture_click(this,'<?php print $macro_omniture; ?>')" href="<?php echo $macro_link ?>?intcmp=<?php print $macro_omniture; ?>">
				<img border="0" src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
			</a>
			<a class="more" onclick="omniture_click(this,'<?php print $macro_omniture; ?>')" href="<?php echo $macro_link ?>?intcmp=<?php print $macro_omniture; ?>"> <?php print t('More'); ?></a>
		</p>

                                        </div>
				</div>
			<?php
			}
			?>
				
			</div>
		</div>
		<?php 
                $total += $itemNmb[$i];
		$i++;
		} ?>
		</div>
			<div class="nav nav-left"><div id="btn-left"><!-- --></div></div>
			<div class="nav nav-right"><div id="btn-right"><!-- --></div></div>
	</div>
	<div id="climaRide">
		<img border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre2_<?php echo $lang;?>.gif" width="741" />
	</div>
	<div id="climaride_content">
		<div id="clima_ride_blocks" class="whitebg">
		<?php
			$t=0;
			$rangeHref = array();
			$apparelMenu = array_pop($primary_links);
			foreach ($apparelMenu['below'] as $range){
				$hrefA = array_shift($range['below']);
				$rangeHref[] = url($hrefA['link']['href']);
			}

			$assoClima = array(
				array(array('Bottoms',$rangeHref[0]),array('Outerwear',$rangeHref[1]),array('Gloves',$rangeHref[4]),array('Warmers',$rangeHref[5])),
				array(array('Bottoms',$rangeHref[0]),array('Outerwear',$rangeHref[1]),array('Gloves',$rangeHref[4]),array('Warmers',$rangeHref[5]),array('Jerseys',$rangeHref[2])),
				array(array('Bottoms',$rangeHref[0]),array('Outerwear',$rangeHref[1]),array('Gloves',$rangeHref[4]),array('Warmers',$rangeHref[5])),
				array(array('Bottoms',$rangeHref[0]),array('Outerwear',$rangeHref[1]),array('Warmers',$rangeHref[5])),
				array(array('Outerwear',$rangeHref[1]),array('Warmers',$rangeHref[5]))
			);	
			foreach($field_ss2012_slide_techno_img as $climaride){ 
				$technoNode = node_load($field_ss2012_climaride_data[$t]['nid']);//node techno
				//
				// to have english version for omniture
				//
				if($technoNode->tnid != $technoNode->nid) {
					$english_node = node_load($technoNode->tnid);
				} else {
					$english_node = $technoNode;
				}

				$query = 'select nid from node where tnid in (select nid from node where title=\''.$english_node->title.'\' and type=\'prodvalcarac\')';
		// echo "download : $query <br />";
		$res = db_query($query);
		$pf = array();
		while ($down_nid = db_fetch_array($res)) {
			$pf[]=$down_nid['nid'];
		}
				
				$technoDesc = $technoNode->body;
				$technoTitle = $technoNode->title;
		?>
				<div class="clima_ride_block">
					<div class="clima_ride_text_block">
						<div class="title helvetica"><?php echo $technoTitle; ?></div>
						<p><?php echo $technoDesc; ?></p>
						<ul>
						<h5><?php echo t('associated ranges:');?></h5>
							<?php foreach ($assoClima[$t] as $assoClimaItem) {?>
							<li><a onclick="omniture_click(this,'landingpage_FWapparel12_<?php echo replace_special_char($english_node->title); ?>')" href="<?php echo $assoClimaItem[1]; ?>?intcmp=landingpage_FWapparel12_<?php echo replace_special_char($english_node->title).'&amp;pf='.implode(',',$pf); ?>"><?php echo t($assoClimaItem[0]); ?></a></li>
							<?php }?>
						</ul>
					</div>
					<img class="climaRide_img" src="<?php print base_path().$field_ss2012_slide_techno_img[$t]['filepath']; ?>" width="520" height="491" />
				</div>		
	
		<?php	
			$t++;
			}
			
			?>
			</div>		
	</div>
	<div id="climaride_nav">
		<a class="climaride_nav active" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/climaride_01.gif');"></a>
		<a class="climaride_nav" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/climaride_02.gif');"></a>
		<a class="climaride_nav" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/climaride_03.gif');"></a>
		<a class="climaride_nav" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/climaride_04.gif');"></a>
		<a class="climaride_nav" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/climaride_05.gif');"></a>
	</div>

	<!--end Clima Ride -->
	<div class="clear"></div>
	<div id="ergoRide">
        	<img class="title" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre3_<?php echo $lang;?>.gif" width="741" />
	
		
		<?php
			$t=0;
			//$technoNode = node_load($field_ss2012_ergoride_data[$t]['nid']);//node techno
			//var_dump($technoNode);die;
			foreach($field_ss2012_ergoride_img as $ergoride){
				$technoNode = node_load($field_ss2012_ergoride_data[$t]['nid']);//node techno
				$technoDesc = $technoNode->body;
				$technoHref = $technoNode->path;
				$technoSubtitle = $technoNode->title;
				$technoImg = base_path().$ergoride['filepath'];
				//
				// to have english version for omniture
				//
				if($technoNode->tnid != $technoNode->nid) {
					$english_node = node_load($technoNode->tnid);
				} else {
					$english_node = $technoNode;
				}
				$omniture = 'landingpage_FWapparel12_'.replace_special_char($english_node->title);
		?>
                <div class="techno_block">
                        <img class="techno_img" src="<?php print $technoImg; ?>" />
                        <p class="techno_subtitle">
                                <b><?php print $technoSubtitle; ?></b>
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
                                <div class="clear"></div>

                  </div>

                        <?php	
                                $t++;
                                }
                                ?>
                
        </div>
        <div class="clear"></div>
        
                <!--end techno -->
                <!-- discover -->
        <div id="discover_block">
            <img class="title" border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/discover_<?php echo $lang;?>.gif" width="741" />
    	
            
		<?php
                $allAppRange = array(
                    array('Bottoms',$rangeHref[0]),
                    array('Outerwear',$rangeHref[1]),
                    array('Jerseys',$rangeHref[2]),
                    array('Underwear',$rangeHref[3]),
                    array('Gloves',$rangeHref[4]),
                    array('Warmers',$rangeHref[5]),
                    array('Socks',$rangeHref[6]));


                foreach ($allAppRange as $urlApp) {
                    ?>
            <a class="ss2012_discover" onclick="omniture_click(this,'landingpage_FWapparel12_discover_range')" href="<?php echo $urlApp[1]; ?>?intcmp=landingpage_FWapparel12_<?php echo replace_special_char($urlApp[0]); ?>" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/discover_<?php echo $urlApp[0]; ?>.jpg');"><?php echo strtoupper(t($urlApp[0])); ?></a>
 
              <?php  }
                ?>
            <div class="clear"></div>
        </div>
        <!--end discover -->
        
	<div id="nl_subscribe">
                <img border="0" src="/sites/default/themes/mavic/images/landingpage/ss2012/titre4_<?php echo $lang;?>.gif?v=2" width="741" />
                <a href="<?php echo url('newsletter',array('absolute'=>TRUE)); ?>?intcmp=landingpage_FWapparel12_newsletter" onclick="omniture_click(this, 'landingpage_FWapparel12_newsletter');" style="background-image:url('<?php echo base_path();?>sites/default/themes/mavic/images/landingpage/ss2012/signup_<?php echo $lang; ?>.gif');"></a>
	</div>
</div>