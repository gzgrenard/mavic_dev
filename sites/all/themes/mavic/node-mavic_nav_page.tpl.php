
<script type="text/javascript" >

$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		$('#main_content').css({'background-color':'transparent', 'padding':0});
	});
</script>
<div id="tabs" class="midlevel">


<?php
	function getSecondaryLinksLevel2Page($titleId, $level2Items, $i, $menu_news, $menu_video, $lang, $technologies_path, $menu_technologies, $node, $breadcrumb, $discipline, $technologies_path_sys)
	{
		if (empty($discipline)){
			$menu = @reset($menu_video);
			$step = 33;
			
		$out = '<script>
		$(document).ready(function() {
				var originalHeight =  $("#submenu_page_MAVICItem").height();
				function changePageHeight() {
						var newHeight = ($("#logo_container").offset().top + $("#logo_container").height()) - $("#submenu_page_MAVICItem").offset().top;
						if($("#submenu_page_MAVICItem").height() < newHeight || (newHeight >= originalHeight && newHeight < $("#submenu_page_MAVICItem").height())) $("#submenu_page_MAVICItem").css("height", newHeight);
				}
				
				$(window).bind("resize", function () {changePageHeight()});
				changePageHeight();
		})();
		</script>';
			$out .= '<div id="submenu_page_MAVICItem" class="submenu_mavic_page submenu_MAVICItem submenu_mavic">
						<div class="submaviccontent2">				
							<div class="right video">
								<div class="buttons">
								<div>
									<div class="right_buttons">			  
										<a class="prev browse left disabled"><img src="' . base_path().path_to_theme() . '/images/carousel_prev.gif" alt="" /></a>
										<a class="next browse right"><img src="' . base_path().path_to_theme() . '/images/carousel_next.gif" alt="" /></a>				
									</div>
									
										<p class="helvetica title">' . t('videos') . '</p></div>
										<div class="left_buttons">
											<img src="'. base_path().path_to_theme() . '/images/more_info.gif" alt=""><a href="' . url($menu['link']['href']) . '" >' . t('All videos') . '</a>
										</div>
									
									<div class="clear"></div>
								</div>
							  <!-- root element for scrollable -->
								<div class="scrollable view-content">
									<div class="items">'
			;
			$z = 0;
			foreach($menu_video as $video) {
				$z++;
				if ($z <= 4){
					$item = menu_get_item($video['link']['href']);
					$itemMap = $item['map'][1];
					$link = url($video['link']['href']);
					$imgpath = $itemMap->field_video_image[0]['filepath'];
					$imgpathsub = substr_replace($imgpath, 'menu', -8, -4);
					if(!file_exists($imgpathsub)){
						$imgpathsub = $itemMap->field_video_img_menu[0]['filepath'];
						if(!file_exists($imgpathsub) || empty($itemMap->field_video_img_menu[0]['filepath'])){
							$imgpathsub = 'sites/default/files/no_image_menu.jpg';
						}
					}
					$imgpathsub = base_path().$imgpathsub;
					if($video['link']['in_active_trail'])
					{
						$link = '#';
						$classe = ' isactive';
					}
					else $classe = '';
					$out.=		'
							<div class="underitem">
								<div id="blockvideo" class="element'.$classe.'">
									<img width="236" alt="'. $itemMap->title .'" class="big" src="'. $imgpathsub . '">
									<a href="'. $link .'" class="link" style="display:none">'. $link .'</a>
								</div>
								<div class="clear mavic_menu_spacer"></div>
								<div class="complement">
											<p class="title"><a href="'. $link .'">'. $itemMap->title .'</a></p>
											<p class="description"><a href="'. $link .'">'	. $itemMap->field_page_description[0]['value'] . '</a></p>
								</div>
							</div>
				';	
				}
			}
			$menu = @reset($menu_technologies);
			if(!empty($menu['below']))
			{
				$menu = reset($menu['below']);
			}
			$out .=			'
									</div>
								</div>
							</div>
							<div class="right">
								'.views_embed_view("news","block_1").'
		
							</div>
							<div class="right techno">
								<div class="buttons">
								<div>
									<div class="right_buttons">			  
										<a class="prev browse left disabled"><img src="' . base_path().path_to_theme() . '/images/carousel_prev.gif" alt="" /></a>
										<a class="next browse right"><img src="' . base_path().path_to_theme() . '/images/carousel_next.gif" alt="" /></a>				
									</div>
									
										<p class="helvetica title">' . t('technologies') . '</p></div>
										<div class="left_buttons">
											<img src="'. base_path().path_to_theme() . '/images/more_info.gif" alt=""><a href="' . url($menu['link']['href']) . '" >' . t('All technologies') . '</a>
										</div>
									
									<div class="clear"></div>
								</div>
							  <!-- root element for scrollable -->
								<div class="scrollable view-content">
									<div class="items">'
			;
			$technocat = array(0 => "305520",1 => "305475",2 => "306355",3 => "305603");
			shuffle($technocat);
                       
			foreach($technocat as $technocatlang){
				$technocattrad = translation_node_get_translations($technocatlang);
				$link = "node/" . $technocattrad[$lang]->nid;
				$item = menu_get_item($link);
				$itemMap = $item['map'][1];
				if($node->nid == $technocattrad[$lang]->nid)
				{
					$link = '#';
					$classe = ' isactive';
					$textcontent = $node->content['body']['#value'];
		
				}
				else
				{
					$link = url($link);
					$classe = '';
					$textcontent = $itemMap->body;
				}
                                
                                foreach($itemMap->field_feature_codes as $ChildFeatureCodeValue){
                                    
                                    $childFeatureCodeFound = '';
					if( file_exists( $technologies_path_sys.'/brother/'.$ChildFeatureCodeValue["value"].'.jpg')){
						$childFeatureCodeFound = $ChildFeatureCodeValue['value'];
						break;
					} 
                                        
				}
				$out.=		'
										<div class="underitem">
											<div id="blocktechno" class="element'.$classe.'">
												<img width="236" height="133" class="big" src="'. $technologies_path.'/brother/'. $childFeatureCodeFound .'.jpg">
												<a href="'. $link .'" class="link" style="display:none">'. $link .'</a>
											</div>
											<div class="clear mavic_menu_spacer"></div>
											<div class="complement">
														<p class="title"><a href="'. $link .'">'. $itemMap->title .'</a></p>
														<p class="description"><a href="'. $link .'">'. $textcontent .'</a></p>
											</div>
										</div>
							';	
		
			}
			$out .=			'
									</div>
								</div>
							</div>
							<div class="left">';
		} else {
			$out = '<div id="submenu_page_MAVICItem" class="submenu_mavic_page submenu_MAVICItem submenu_mavic disc_mavic_nav">
						<div class="submaviccontent2">
							<div class="left">';
		}
		foreach($level2Items as $item)
		{
			$out.= '<div class="level2">';
	
			$out.= '<span class="helvetica">'.$item->title.'</span>';
	
			if (isset($item->below)) {
				$out.='<div class="wraplevel3" >';
				foreach($item->below as $belowItem)
				{
					$out .='<a class="level3" href="'.url($belowItem['link']['href']).'">'.$belowItem['link']['title'].'</a>';
				}
				$out.='</div>';
				$out .= '<div class="level3_separator"></div>';
			}
			$out .= '</div>';
		}
		$out.= '		</div>
					</div>
				</div>';
		
		$out .= '<script>
				$(document).ready(function() {
						var originalHeight =  $("#submenu_page_MAVICItem").height();
						function changePageHeight() {
								var newHeight = ($("#logo_container").offset().top + $("#logo_container").height()) - $("#submenu_page_MAVICItem").offset().top;
								if($("#submenu_page_MAVICItem").height() < newHeight || (newHeight >= originalHeight && newHeight < $("#submenu_page_MAVICItem").height())) $("#submenu_page_MAVICItem").css("height", newHeight);
						}

						$(window).bind("resize", function () {changePageHeight()});
						changePageHeight();
				});
		</script>';
	
		return $out;
	}
	
	
	
	$output2 = '';
	//
	// MAVIC link
	//
	$class = 'helvetica';
	switch($active_menu_name) {
		case 'menu-photo' :
		case 'menu-menu-technologies-'.$lang :
		case 'menu-videos' :
		case 'menu_athletes' :
		case 'menu-sport' :
		case 'menu-news' :
		case 'menu-history' :
		case 'menu-assistance-'.$lang :
		case 'menu-careers' :
			$class.= ' current';
	}
	$level1ItemTitleId = 'MAVICItem';
	$level2Items = array();
	/*
	 if($menu = @reset($menu_photo)) { // menu photo ----------------------
	$level2Item = new stdClass();
	$level2Item->title = t('photogallery');
	$m = @reset($menu['below']);
	if(!empty($m['link']['href'])) {
	$level2Item->href = $m['link']['href'];
	$level2Items[] = $level2Item;
	}
	}
	*/
	$level2Item = new stdClass();// menu sport ----------------------
	$level2Item->title = t('sport');
	$level2Item->below = array();
	if($menu = @reset($menu_athlete)) {
		// menu athlete
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('athlete');
		$level2Item1['link']['href'] = $menu['link']['href'];
		$level2Item->below[] = $level2Item1;
	}
	if($menu = @reset($menu_assistance)) {
		$level2Item1 = array('link'=>array());// menu neutral support  ----------------------
		if(!empty($menu['link']['href'])) {
			$level2Item1['link']['title'] = $menu['link']['title'];
			$level2Item1['link']['href'] = $menu['link']['href'];
			$level2Item->below[] = $level2Item1;
		}
		$level2Item1 = array('link'=>array());// menu events  ----------------------
		$menu = next($menu_assistance);
		if(!empty($menu['link']['href'])) {
			$level2Item1['link']['title'] = $menu['link']['title'];
			$level2Item1['link']['href'] = $menu['link']['href'];
			$level2Item->below[] = $level2Item1;
		}
	}
	
	$level2Items[] = $level2Item;
	$level2Item = new stdClass(); // menu where to buy ----------------------
	$level2Item->title = t('where to buy');
	$level2Item->below = array();
	$level2Item1 = array('link'=>array()); // menu shopfinder ----------------------
	$level2Item1['link']['title'] = t('find a dealer');
	$level2Item1['link']['href'] = 'shopfinder';
	$level2Item->below[] = $level2Item1;
	$level2Item1 = array('link'=>array()); // menu shopinshop ----------------------
	$level2Item1['link']['title'] = t('shop in shops');
	$level2Item1['link']['href'] = 'shopinshop';
	$level2Item->below[] = $level2Item1;
	$level2Item1 = array('link'=>array()); // menu distributor ----------------------
	$level2Item1['link']['title'] = t('find a distributor');
	$level2Item1['link']['href'] = 'distributor';
	$level2Item->below[] = $level2Item1;
	$level2Items[] = $level2Item;
	
	$level2Item = new stdClass(); // menu company ----------------------
	$level2Item->title = t('company');
	$level2Item->below = array();
	$level2Item1 = array('link'=>array()); // menu who we are ----------------------
	$level2Item1['link']['title'] = t('who we are?');
	$level2Item1['link']['href'] = str_replace(array('&','?','='),'',$level2Item1['link']['title']);
	$level2Item->below[] = $level2Item1;
	if($menu = @reset($menu_history)) {
		// menu history ----------------------
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('history');
		$m = reset($menu['below']);
		$level2Item1['link']['href'] = $m['link']['href'];
		$level2Item->below[] = $level2Item1;
	}
	if ($menu = @reset ($menu_careers)) {
		// menu careers ----------------------
		$level2Item1 = array('link'=>array());
		$level2Item1['link']['title'] = t('careers');
		$level2Item1['link']['href'] = $menu['link']['href'];
		$level2Item->below[] = $level2Item1;
	}
	$level2Item1 = array('link'=>array()); // menu service ----------------------
	$level2Item1['link']['title'] = t('Mavic Service Center');
	$level2Item1['link']['href'] = str_replace(array('&','?','='),'',$level2Item1['link']['title']);
	$level2Item->below[] = $level2Item1;
	$level2Items[] = $level2Item;
	
	
	$output2.= getSecondaryLinksLevel2Page($level1ItemTitleId,$level2Items, $i, $menu_news, $menu_video, $lang, $technologies_path, $menu_technologies, $node, $breadcrumb, $discipline, $technologies_path_sys);
	
	print $output2;
	?>
</div>