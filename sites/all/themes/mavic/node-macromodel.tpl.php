	<script type="text/javascript" >
		
		$(document).ready(function() {
			$("#body-background").ezBgResize();
			checkSize();

			createTabsAction();
			//getSessionProductCompare();
			displayTabs();

			/* Visuel */
			$('#product .visuel:first').show();

			/* Colors*/
			$('#product .change-color').css('opacity','1');
			$('#product .change-color:first').css('opacity','0.3');

			/* Feature */
			$('#change_image a.feature-image').show();
			$('#change_image a.feature-image-notooltip').show();
			$('#features_content .feature-content').show();

			//Get feature picture big
			$('a.feature-image,a.feature-image-notooltip').click(function() {
				showZoom(this.href,$(this).find('img').attr('alt'));
				return false;
			});
			
			//Zoom on image
			$('#product .picture').click(function(){
				omniture_click(document.getElementById('zoom'), 'Zoom');
				showZoom($('#zoom').attr('href'),$('#zoom').attr('alt'));
				return false;
			});

			$('#closeZoom_wrapper').click(function(){
				hideMegaZoom();
				hideZoom();
			});
			$('#closeZoom').click(function(e){
				e.stopPropagation(); //$('#closeZoom_wrapper').click() will not fire
				hideMegaZoom();
				hideZoom();
			});

			//hover for #closeZoom_wrapper in ie6
			if ($.browser.msie && $.browser.version.substr(0,1)<7) {
				$('#closeZoom_wrapper').mouseenter(function(){
					$(this).addClass('hover');
				}).mouseleave(function(){
					$(this).removeClass('hover');
				});
			}

			/* Color switch*/

			//Associated and size init
			$('a.associated-off').hide();
			$('a.associated-on').show();
			$('p.size-off').hide();
			$('p.size-on').show();
			$('#change_color a').click(function() {

				//change opacity for others
				$('#product .change-color').css('opacity','1');
				$(this).css('opacity','0.3');

				$('#change_view a.button_view').show();//Link to change view available
				var associated = $(this).attr("name").split('_');
				var articleId = $(this).children(":first").attr("name");

				$('a.associated').hide();
				$('p.size').hide();
				$('p.size-'+articleId).show();
				for ( var i=0;i<associated.length;i++) {
					$('a.associated-'+associated[i]).show();
				}
				$color_href = $(this).attr("href");

				var $imgVisuel = $('#product img:visible');
				$('#product img.visuel').hide();
				if($imgVisuel.hasClass('visuel-front')) {
					$('#product img.visuel-front-'+articleId).show();
					$('#zoom').attr("href", $color_href.replace('normal', 'zoom').replace('.jpg', '<?php echo $altview_image_suffix; ?>'));
				}<?php if (isset($altview_image_suffix2)){ ?> else if($imgVisuel.hasClass('visuel-alt2')) {
					$('#product img.visuel-alt2-'+articleId).show();
					$('#zoom').attr("href", $color_href.replace('normal', 'zoom').replace('.jpg', '<?php echo $altview_image_suffix2; ?>'));
				}<?php } ?> else {
					$('#product img.visuel-'+articleId).show();
					$('#zoom').attr("href", $color_href.replace('normal', 'zoom'));

				}
				$('#zoom').show();
				$('#change_view').show();

				return false;
			});
                                                      // Set color from url anchor
                                                      var colorAnchor = window.location.hash.substring(1);
                                                      $('#altColor'+colorAnchor).trigger('click');
                                                      
			/* Front/Rear wheel */
			$('a.button_view').click(function() {
				$(this).parent().children().removeClass('button-view-active button-view-rear-active button-view-front-active');
			});
			$('#front_wheel').click(function() {
				//Get article/color of active image
				var articleId = $('#product img.visuel:visible').attr("name");
				if(articleId) {
					$('#product img.visuel').hide();
					$('#product .visuel-front-'+articleId).show();
					$(this).addClass('button-view-active button-view-front-active');
					var $visibleSrc = $('#product img.visuel:visible').attr("src");
					$('#zoom').attr("href", $visibleSrc.replace('normal', 'zoom'));
				}
				//
				// omniture event 
				//
				omniture_click(this, 'Front view');
			});
			$('#rear_wheel').click(function() {
				var articleId = $('#product img.visuel:visible').attr("name");
				if(articleId) {
					$('#product img.visuel').hide();
					$('#product .visuel-'+articleId).show();
					$(this).addClass('button-view-active button-view-rear-active');
					var $visibleSrc = $('#product img.visuel:visible').attr("src");
					$('#zoom').attr("href", $visibleSrc.replace('normal', 'zoom'));
				}
				//
				// omniture event 
				//
				omniture_click(this, 'Rear view');
			});
			$('#alt2_wheel').click(function() {
				var articleId = $('#product img.visuel:visible').attr("name");
				if(articleId) {
					$('#product img.visuel').hide();
					$('#product .visuel-alt2-'+articleId).show();
					$(this).addClass('button-view-active button-view-rear-active');
					var $visibleSrc = $('#product img.visuel:visible').attr("src");
					$('#zoom').attr("href", $visibleSrc.replace('normal', 'zoom'));
				}
				//
				// omniture event 
				//
				omniture_click(this, 'Rear view');
			});

		});
		var megaZoomImgSrc = "<?php echo $product_path.'/megazoom/'.$list_color[$default_color]->url_default; ?>";
		var activeFamily = '<?php echo str_replace('node/','',$breadcrumb[3]['link']['href'])?>';
		var basePath ='<?php echo base_path().$language; ?>';
		var t_compare = "<?php echo t('COMPARE')?>";
	</script>
	<?php
			//
			// zoom et visuels
			//
	?>
	<div id="product">
		<div class="left">
			<div id="zoomBar" >
				<a onclick="omniture_click(document.getElementById('zoom'), 'Zoom');showZoom(this.href,'<?php echo $title;?>'); return false; " alt="<?php echo $title;?>" id="zoom" class="button_view" href="<?php echo $product_path.'/zoom/'.$list_color[$default_color]->url_default; ?>"><?php print t('ZOOM')?></a>
				<img id="corner" src="<?php echo base_path().path_to_theme();?>/images/corner.gif" alt="" />
			</div>
			<div class="picture">
<?php 
				$hasRear = false;
				$hasAlt2 = false;
				$hasAssociated = false;
				$imgColorProduit = array();
				$sizeColorProduit = array();
				$associatedColorProduit = array();
                                
                                                                        // Manage alternative
                                                                        $fullAltView = array();
                                                                        $maxAltByColor = 0;
                                                                        $refColor = 0;
                                                                        $errorFlag = false;
                                                                        $alternatives='';
                                                                        $nb_altern = 0;
                                                                        
				foreach($list_color as $key => $color) :
					if(!empty($color->url_altview))  $hasRear = true;
					if(!empty($color->url_altview2))  $hasAlt2 = true;
?>
					<img name="<?php echo $color->title;?>" style="display: none;" class="visuel visuel-<?php echo $color->title;?>" src="<?php echo $product_path.'/normal/'.$color->url_default; ?>?v=1" alt="<?php echo $title;?>" />
<?php 
					if($hasRear) { 
?>
						<img name="<?php echo $color->title;?>" style="display: none;" class="visuel visuel-front visuel-front-<?php echo $color->title;?>" src="<?php echo $product_path.'/normal/'.$color->url_altview; ?>?v=1" alt="<?php echo $title;?>" />
<?php 
					} 
					if($hasAlt2) { 
?>
						<img name="<?php echo $color->title;?>" style="display: none;" class="visuel visuel-alt2 visuel-alt2-<?php echo $color->title;?>" src="<?php echo $product_path.'/normal/'.$color->url_altview2; ?>?v=1" alt="<?php echo $title;?>" />
<?php 
					} 

					// code decale pour determine si il y a des produits associes
					foreach($color->node_associated as $assoc) :
						$hasAssociated = true;
						break;
					endforeach;

					//
					// img color produit
					//
					if(!empty($color->title)){
						$i=0;
						$names = '';
						foreach($color->node_associated as $assoc) :
							if($i!=0)
								$names.='_';
							$names.= $assoc->title.'-'.$color->title;
							$i++;
						endforeach;
						$imgColorProduit[] = '<a id="altColor'.$color->title.'" name="'.$names.'" class="change-color" href="'.$product_path.'/normal/'.$color->url_default.'" onclick="omniture_click(this,\'color view\')"><img name="'.$color->title.'" src="'.$product_path.'/colors/'.$color->url_default.'?v=1" alt="'.$title.'" /></a>';
					}
                                        
                                                                                        //
                                                                                        //  alternate product
                                                                                        //
                                                                                        // Get all file with the item tag name
                                                                                        foreach (glob($product_path_sys.'/alternatives/mini/'.$color->title.'_*.jpg') as $altViewPicture) {
                                                                                             $alternativesView[] = $altViewPicture;
                                                                                         }
                                                                                         // Order all file by name and at the last _black images
                                                                                         $orderAltView = $alternativesView;
                                                                                         $countColorAltView = 0;
                                                                                         foreach ($alternativesView as $index => $alternativeView) {
                                                                                             $countColorAltView++;
                                                                                             if (strpos(basename($alternativeView),'_black.jpg') !== false) {
                                                                                                 unset($orderAltView[$index]);
                                                                                                 array_push($orderAltView, $alternativeView);
                                                                                             }
                                                                                         }
                                                                                         unset($alternativesView);
                                                                                         
                                                                                         // Store on global array for all color
                                                                                         if ($countColorAltView > $maxAltByColor) {
                                                                                             if ($maxAltByColor > 0) $errorFlag = true;
                                                                                             $maxAltByColor = $countColorAltView;
                                                                                             $refColor = $color->title;
                                                                                         } else if ($maxAltByColor > 0) $errorFlag = true;
                                                                                         $fullAltView[$color->title] = array('data' =>$orderAltView, 'count' => $countColorAltView);
                                                                                         
					//
					// size depending on color
					//
					if(!empty($color->field_size[0]['value'])) {
						$class = 'size size-'.$color->title;
						if($key == $default_color) $class .= ' size-on';
						else $class .= ' size-off';
						$sizeColorProduit[] = '<p class="'.$class.'"><span class="bold">'.t('Sizes:').'</span> '.$color->field_size[0]['value'].' '.t('(UK)').'<br /></p>';
					}

					//
					// associated produtc depending on color
					//
					//Debug purpose for this line
						//$color->node_associated[1] = clone $color->node_associated[0]; $color->node_associated[1]->title = 34;
					foreach($color->node_associated as $assoc) :
						$class ="associated associated-$assoc->title".'-'."$color->title";
						if($default_color==$key) $class.=" associated-on"; else $class.=" associated-off";
						
						$associatedColorProduit[] = '<a class="'.$class.'" href="'.$assoc->macro_path.'?'.$assoc->title.'">';
						if(file_exists($product_path_sys.'/associated/'.$assoc->title.'.jpg')){
							$associatedColorProduit[] = '<img onmouseover="overImage(\''.$assoc->nid.'\')" src="'.$product_path.'/associated/'.$assoc->title.'.jpg?v=1" alt="'.$assoc->macro_title.'" />';
						}
						else{
							$associatedColorProduit[] = '<img onmouseover="overImage(\''.$assoc->nid.'\')" src="'.$product_path.'/associated/default.jpg" alt="'.$assoc->macro_title.'" />';
						}
						$associatedColorProduit[] = '<div id="altpop'.$assoc->nid.'" name="altpop'.$assoc->nid.'" class="altpop"><b>'.$assoc->macro_title.'</b><br/>'.$assoc->macro_usp.'</div></a>';
					endforeach;		
				endforeach;
                                                                        
                                                                        // Log page product : mistake on alternate view picture
                                                                        if ($errorFlag) {
                                                                            $logError = "";
                                                                            foreach($fullAltView as $colorTitle => $altColor) {
                                                                                if ($colorTitle != $refColor) {
                                                                                    $logError .= "\"$product_path_sys/alternatives/mini/$colorTitle*\" not use remplaced by \"$product_path_sys/alternatives/mini/$refColor*\"\r";
                                                                                }
                                                                            }
                                                                            error_log("Page product alternate \"".$_SERVER["REQUEST_URI"]."\" view error :\r$logError\n\r", 3, './logs/product_alt_view.log');
                                                                            
                                                                        }
                                
                                                                        // Render all atlernative view
                                                                        foreach($fullAltView[$refColor]['data'] as $altView) {
                                                                                $fileName = basename($altView);
                                                                                // Display alternative only if file exist for mini/zoom/megazoom
                                                                                if ( file_exists($product_path_sys.'/alternatives/mini/'.$fileName) &&
                                                                                     file_exists($product_path_sys.'/alternatives/zoom/'.$fileName) &&
                                                                                     file_exists($product_path_sys.'/alternatives/megazoom/'.$fileName)) {
                                                                                        $alternatives.='
                                                                                            <a onclick="omniture_click(this, \'Alternate view\');" class="feature-image-notooltip" style="display: none;" href="'.$product_path.'/alternatives/zoom/'.$fileName.'">
                                                                                                    <img src="'.$product_path.'/alternatives/mini/'.$fileName.'?v=1" alt="'.$title.'"/>
                                                                                            </a>';
                                                                                        $nb_altern++;
                                                                                }
                                                                        }
?>

				<img  class="visuel visuel-feature" style="display:none;" src="" alt="" />
			</div>

			<div id="zoomBox" onclick="showMegaZoom()">
			</div>
			<div id="megaZoom" class="zoom" onclick="hideMegaZoom()" >
				<img id="megaZoomImg" style="" src=""/>
			</div>
			<div id="closeZoom_wrapper">
				<a onclick="hideMegaZoom();hideZoom();" id="closeZoom" class="button_view"><?php print t('CLOSE')?></a>
			</div>
			
			<?php
				//
				// bouton front-rear + trait gris
				// + code decale pour determiner si il y a
				// -des vu alternatives
				// -des image de features
				//
				global $listFeature;
				$listFeatureTri = array();
				foreach($listFeature as $feature) {
                                                                                if(!isset($listFeatureTri[$feature['type']])) $listFeatureTri[$feature['type']] = array();
                                                                                $listFeatureTri[$feature['type']][] = $feature;
                                    
                                                                                if ($feature['img'] != null) {
                                                                                    $featureImg = $features_path_sys.'/mini/'.$feature['img'].'_*.jpg';
                                                                                    $featuresView = array();
                                                                                    foreach (glob($featureImg) as $altViewPicture) {
                                                                                          $featuresView[] = $altViewPicture;
                                                                                     }
                                                                                     // Order all file by name and at the last _black images
                                                                                    $orderAltView = $featuresView;
                                                                                    foreach ($featuresView as $key => $alternativeView) {
                                                                                        if (strpos(basename($alternativeView),'_black.jpg') !== false) {
                                                                                            unset($orderAltView[$key]);
                                                                                            array_push($orderAltView, $alternativeView);
                                                                                        }
                                                                                    }
                                                                                    unset($alternativesView);
                                                                                    
                                                                                    foreach($orderAltView as $featureView) {
                                                                                        $fileName = basename($featureView);
                                                                                         if (   file_exists(".$features_path/mini/$fileName") &&
                                                                                                file_exists(".$features_path/zoom/$fileName") &&
                                                                                                file_exists(".$features_path/megazoom/$fileName")) {
                                                                                                $alternatives.='
                                                                                                <a onclick="omniture_click(this, \'Alternate view\');" class="feature-image" style="display: none;" href="'."$features_path/zoom/$fileName".'">
                                                                                                        <img src="'."$features_path/mini/$fileName".'?v=1" alt="'.$feature['feature'].'"   onmouseover="overImage(\'123456'.$nb_altern.'\');"/>
                                                                                                        <DIV id=altpop123456'.$nb_altern.' class=altpop name="altpop123456'.$nb_altern.'"><B>'.$feature['feature'].'</B></DIV>
                                                                                                </a>';
                                                                                                $nb_altern++;
                                                                                        }
                                                                                    }
                                                                                }
				}
				
				if(count($list_color)>1 || $hasRear || $hasAlt2 || $alternatives) $change_view_bg = "change_view_bg";
				else $change_view_bg = "";
			?>
			<DIV id="navigation_box">
			<div id="change_view" class="<?php echo $change_view_bg;?>" >
				<?php 	if($hasRear) :?>
						<a id="rear_wheel" href="javascript:void(0)" class="button_view  button-view-rear button-view-rear-active button-view-active"><?php print ($altview_label[1])?></a>
						<a id="front_wheel" href="javascript:void(0)" class="button_view button-view-front"><?php print ($altview_label[0]); ?></a>
					<?php 	if($hasAlt2) :?>
						<a id="alt2_wheel" href="javascript:void(0)" class="button_view  button-view-rear"><?php print ($altview_label[2])?></a>
					<?php 	endif;?>
				<?php 	endif;?>
			</div>
			<div class="clear"></div>
			<?php 
				// code decale pour determine si il y a des produits associes
		/*		$hasAssociated = false;
				foreach($list_color as $key => $color) :
					foreach($color->node_associated as $assoc) :
						$hasAssociated = true;
						break;
					endforeach;
				endforeach; */
			?>
			<?php if(count($list_color)>1):?>
				<div id="change_color">
				<?php 	
						//
						// img color produit
						//
						print implode("\n",$imgColorProduit);
/*						foreach($list_color as $key => $color) :
							if(!empty($color->title)){
								$i=0;
								$names = '';
								foreach($color->node_associated as $assoc) :
									if($i!=0)
										$names.='_';
									$names.= $assoc->title.'-'.$color->title;
									$i++;
								endforeach;
							?>
							<a name="<?php print $names;?>" class="change-color" href="<?php echo $product_path.'/normal/'.$color->url_default; ?>" onclick="omniture_click(this,'color view')";>
								<img name="<?php print $color->title?>" src="<?php echo $product_path.'/colors/'.$color->url_default; ?>" alt="<?php print $title?>" />
							</a>
				<?php
							}
						endforeach; */?>
					<div class="clear"></div>
				</div>
			<?php else: ?>
			<div class="no_color"><!-- --></div>
			<?php endif;?>


			<?php if(!empty($field_featurenode[0]['nid'])) : ?>
				<div class="clear"></div>

				<?php
				
				//
				// features views and alternative views
				//
				if($alternatives)
				{
					//fill with empty slots
					while( ($nb_altern)%4 )
					{
						$nb_altern++;
						$alternatives.='<a class="empty">&nbsp;</a>';
					}
					?>
					<div id="change_image" >
						<?php echo $alternatives; ?>
					</div>
					<?php
				}
				?>
		<?php endif; ?>
				</DIV>
				<div class="clear"></div>

		</div>

		
		
		<div class="right">


			<h1 class="helvetica title_product">
			<?php if($field_sscnode[0]['nid'])echo '<img src="'.$theme_images.'/logos/ssc_product.gif" alt="'.t('Special Service Course. The Mavic SSC label products are developed in close collaboration with our top athletes. These products meet the strict performance demands of pro riders, and are made for pro cycling.').'" />';?>

			<?php echo $title;?></h1>
			<p class="bold first">
				<?php 
					echo $field_usp[0]['value'];
					if($field_new_product[0]['value'] == 1) {
						if(!empty($field_usp[0]['value'])) echo ' - ';
				?>
						<span id="new_product"><?php echo t('new') ?></span>
				<?php } ?>
			</p>
			<p><?php echo $node->content['body']['#value']; ?></p>

			<p><?php if($field_altiumnode[0]['nid'])echo '<img src="'.$theme_images.'/logos/altium_produit.gif" alt="'.t('The altium label identifies Mavic’s most progressive, innovative and technical softgoods.').'" />';?></p>
			<div class="product_sharing">
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
				<div class="addthis_toolbox">
					<div class="custom_images">
						<a class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" alt="<?php print t('Share to Facebook'); ?>" />
						</a>
						<a class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" alt="<?php print t('Share to Twitter'); ?>" />
						</a>
						<a class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
					</div>
				</div>
			<!-- AddThis Button END -->
			</div>
			<p class="actions">
				<img src="<?php echo base_path().path_to_theme();?>/images/product_compare.gif" alt="" class="product_compare" />
				<a href="javascript:storeOneProductToCompare('<?php echo $nid?>');" onclick="omniture_click(this, 'Compare');"><?php print (t('PRODUCT COMPARE'))?></a>
				<img src="<?php echo base_path().path_to_theme();?>/images/print.gif" alt="" class="print" />
				<a href="javascript: window.print();" onclick="omniture_click(this, 'Print:<?php echo $english_node->title;?>');"><?php print (t('PRINT'))?></a>
			</p>
			<div class="clear"></div>
	<?php if(!empty($field_killerpointmacrolb[0]['value'])) :?>
			<div class="yellow">
			<h2 class="helvetica"><?php print (t('KEY BENEFITS'))?></h2>
			</div>
			<?php foreach($field_killerpointmacrolb as $i => $killer) : ?>
				<?php if(!empty($killer['value'])): ?>
					<p>
						<span class="bold"><?php echo nl2br($killer['value']); ?></span><br />
						<?php echo nl2br($field_kcbarglb[$i]['value']); ?>
					</p>
				<?php endif; ?>
			<?php endforeach;?>
		<?php endif; ?>
		<?php
			print implode("\n",$sizeColorProduit);
		/*
			foreach($list_color as $key => $color) :
				if(!empty($color->field_size[0]['value'])) {
					$class = 'size size-'.$color->title;
					if($key == $default_color) $class .= ' size-on';
					else $class .= ' size-off';
					?>
					<p class="<?php echo $class; ?>">
						<span class="bold"><?php echo t('Sizes:');?></span> <?php echo $color->field_size[0]['value'].' '.t('(UK)');?><br />
					</p>
					<?php
				}
			endforeach; */
		?>
		<?php 
		if($breadcrumb[2]['link']['options']['attributes']['title'] != 'helmets_helmets'){ //temporary removal of weight label for helmets
		if(!empty($field_weight_label[0]['value'])) { ?>
			<p>
				<?php
					foreach($field_weight_label as $key => $label) {
						if(!empty($label['value']) && !empty($field_weight[$key]['value'])) {
							if($key == 0 && count($field_weight_label) > 1) {
								echo '<span class="bold">';
								echo t($label['value'].':');
								echo ' '.$field_weight[$key]['value'].'<br />';
								echo '</span>';
							} else if($key > 0) {
								echo t($label['value'].':');
								echo ' '.$field_weight[$key]['value'].'<br />';
							} else {
								echo '<span class="bold">';
								echo t($label['value'].':');
								echo '</span>';
								echo ' '.$field_weight[$key]['value'].'<br />';
							}
						}
					}
				?>
			</p>
		<?php }
		}//end temp weight removed
		if($hasAssociated) :?>
			<div class="yellow">
			<h2 class="helvetica"><?php print (t('ASSOCIATED PRODUCTS'))?></h2>
			</div>
			<div id="content_gamme_items" >
			<div id="associated_products" >
		<?php //foreach($field_associated as $assoc) { echo $assoc['view'];}?>
		<?php
			print implode("\n",$associatedColorProduit);
/*
				foreach($list_color as $key => $color) :
						//Debug purpose for this line
						//$color->node_associated[1] = clone $color->node_associated[0]; $color->node_associated[1]->title = 34;
					foreach($color->node_associated as $assoc) :
						$class ="associated associated-$assoc->title".'-'."$color->title";
						if($default_color==$key) $class.=" associated-on"; else $class.=" associated-off";?>
						<a class="<?php echo $class?>" href="<?php print $assoc->macro_path.'?'.$assoc->title; ?>">
							<?php if(file_exists($product_path_sys.'/associated/'.$assoc->title.'.jpg')) :?>
							<img onmouseover="overImage('<?php echo $assoc->nid ?>')" src="<?php echo $product_path.'/associated/'.$assoc->title.'.jpg';?>" alt="<?php print $assoc->macro_title ?>" />
							<?php else:?>
							<img onmouseover="overImage('<?php echo $assoc->nid ?>')" src="<?php echo $product_path.'/associated/default.jpg';?>" alt="<?php print $assoc->macro_title ?>" />
							<?php endif;?>
							<div id="altpop<?php echo $assoc->nid?>" name="altpop<?php echo $assoc->nid?>" class="altpop">
								<b><?php echo $assoc->macro_title ?></b><br/>
								<?php echo $assoc->macro_usp ?>
							</div>
						</a>
		<?php 		endforeach;
				endforeach;

*/				?>
			</div>
			</div>
	<?php endif; ?>
		</div>
		<div class="clear"></div>
	</div>

	<?php

		$nid_family = substr($breadcrumb[3]['link']['link_path'],5); // recupo family
		$nid_categ = substr($breadcrumb[2]['link']['link_path'],5); // recupo category

		//
		// gestion download associes
		//
		$query = 'select n.nid, count(a.nid) as archive from content_field_download_assoc c INNER JOIN node n USING (nid) LEFT JOIN content_field_download_archive a ON (a.field_download_archive_nid=c.nid) '.
						'where (c.field_download_assoc_nid="'.$nid.'" or c.field_download_assoc_nid="'.$nid_family.'") and n.status=1 group by n.nid';
		// echo "download : $query <br />";
		$res = db_query($query);
		$list_download = array();
		$list_archived_download = array();
		while ($down_nid = db_fetch_array($res)) {
			if($down_nid['archive'] > 0)
				$list_archived_download[] = node_load($down_nid['nid']);
			else
				$list_download[] = node_load($down_nid['nid']);
		}

		//
		// gestion news associes
		//
		$list_news = array();
		$query = 'select nid from (select distinct n.nid, t.field_news_date_value from content_field_news_product c INNER JOIN node n using (nid) INNER JOIN content_type_news t using (nid) '.
						'where c.field_news_product_nid="'.$nid.'" and n.status=1 UNION '.
				'select distinct n.nid, t.field_news_date_value from content_field_news_family c INNER JOIN node n using (nid) INNER JOIN content_type_news t using (nid)'.
						'where c.field_news_family_nid="'.$nid_categ.'" and n.status=1) as t '.
						'order by t.field_news_date_value desc';
        $res = db_query($query);
	//	echo "product news : $query <br />";
		while ($news_nid = db_fetch_array($res)) {
			$list_news[$news_nid['nid']] = node_load($news_nid['nid']);
		}
		
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
	?>
	<div id="tabs" class="tab_space" >
		<div id="tabs_buttons">
			<?php if(!empty($field_technologienode[0]['nid'])) : ?>
				<div id="technologies" onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab helvetica active"><?php print (t('TECHNOLOGIES'))?></div>
			<?php $alreadyActive = true; endif; ?>
			<?php if(!empty($field_featurenode[0]['nid'])) : $classActive = ''; if(!$alreadyActive) $classActive = ' active';?>
				<div id="features" onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab helvetica<?php echo $classActive;?>"><?php print (t('FEATURES'))?></div>
			<?php $alreadyActive = true; endif; ?>
			<?php if(!empty($list_news)) : $classActive = ''; if(!$alreadyActive) $classActive = ' active';?>
				<div id="relatednews" onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab helvetica<?php echo $classActive;?>"><?php print (t('RELATED NEWS'))?></div>
			<?php $alreadyActive = true; endif; ?>
			<?php $classActive = ''; if(!$alreadyActive) $classActive = ' active';?>
			<div id="shopfinder" onmouseout="outTab(this)" onmouseover="overTab(this)" onclick="sf_initialize()" class="tab helvetica<?php echo $classActive;?>"><?php print (t('find a store'))?></div>
			<?php 
// désactivation temporaire de la gallery photo car on a un pb d'espace dans la barre d'onglet
				$menu = @reset($menu_photo);
				$m = @reset($menu['below']);
				if(!empty($m['link']['href']) && FALSE) { // si il n y a pas d album on desactive l'onglet gallery
					$galleria_act = true;
			?>
					<div id="gallerytab" onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab helvetica"><?php print (t('GALLERY'))?></div>
			<?php 
				} else {
					$galleria_act = false;
				}
			?>
			<?php if(!empty($list_download)||!empty($list_archived_download)) : ?>
				<div id="downloads" onmouseout="outTab(this)" onmouseover="overTab(this)" class="tab helvetica"><?php print (t('manuals'))?></div>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<?php if(!empty($field_technologienode[0]['nid'])) : ?>
		<div class="tab_content technologies" id="technologies_content">
			<?php foreach($field_technologienode as $techno) : ?>
				<?php echo $techno['view'];?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<?php if(!empty($field_featurenode[0]['nid'])) : ?>
		<div class="tab_content features" id="features_content">
			<?php
			$i=0;
			foreach($listFeatureTri as $type => $sublistfeature) : ?>
				<p class="color<?php echo ($i%2); ?> feature-content" style="display: none;">
					<span class="bold"><?php echo $type ?></span><br />
					<?php foreach($sublistfeature as $feature) { ?>
						<?php echo $feature['feature'];?><br />
					<?php } ?>
				</p>
			<?php
			$i++;
			endforeach; ?>
			<div class="clear"></div>
		</div>
		<?php endif; ?>
		<?php if($galleria_act) {
// désactivation temporaire de la gallery photo car on a un pb d'espace dans la barre d'onglet
// de plus l'id="gallery" pose problème car il n'est deja stylé pour ailleurs et pose un pb de retour à la ligne

		?>
		<!-- photo gallery -->
			<div class="tab_content gallery" id="gallery_content">
				<script src="<?php echo base_path().path_to_theme();?>/js/galleria/src/galleria.js"></script>
				<script src="<?php echo base_path().path_to_theme();?>/js/galleria/src/plugins/galleria.flickr.js"></script>
				<script src="<?php echo base_path().path_to_theme();?>/js/galleria/src/themes/mavic/galleria.mavic.js"></script>
				<script type="text/javascript" >
					
					// initialize the plugin
	//				var flickr = new Galleria.Flickr('4af2660daf4fe80e729ad7cbd6589710'); 

					flickr_gallery.getTags('<?php echo $node->field_modelco[0]['value']; ?>',display_set);

					// inject a photset into galleria
	/*				flickr.getUserTag({user:'mavicssc', tag:'<?php echo $node->field_modelco[0]['value']; ?>'},
									{size:'medium'}, 
									function(data) {$('#galleria').galleria({data_source:data});}
					);*/
					
				</script>
				<div id="galleria" style="width:709px;height:429px;margin:15px 0px 0px 0px"></div>
				<div class="clear"></div>
			</div>
		<?php } ?>
		<div class="tab_content relatednews" id="relatednews_content">
			<?php foreach($list_news as $news) { ?>
				<?php
					$month = $trad[substr($news->field_news_date[0]['value'],5,2)];
					$year = substr($news->field_news_date[0]['value'],0,4);
					$day = (int)substr($news->field_news_date[0]['value'],8,2);
				?>
				<div class="element" onclick="document.location.href='<?php echo url($news->path) ?>'" >
					<div class="imageslot">
						<img height="108" src="<?php echo str_replace('.jpg','_m.jpg',$news->field_news_picture_flickr[0]['value']) ?>" class="big" alt="<?php print $news->title ?>" />
					</div>
					<div class="contentslot">
						<p class="title"><b><?php print $news->title ?></b></p>
						<p class="text">
							<?php echo truncate_utf8($news->field_news_intro[0]['value'],190,true,true) ?>&nbsp;<span class="product_assoc_date_news">(<?php echo $day.' '.$month.' '.$year ?>)</span>
						</p>
						<p>
							<img src="<?php echo base_path().path_to_theme();?>/images/more_info.gif" alt="<?php print $news->title ?>" />
							<a href="<?php echo url($news->path) ?>"class="moreinfos"><?php echo t('More infos') ?></a>
						</p>
					</div>
					<div class="clear"></div>
				</div>
			<?php } ?>
		</div>
		<div class="tab_content shopfinder" id="shopfinder_content">
			<?php 
				include('shopfinder.php');
				if(!$alreadyActive) { 
			?>
					<script type="text/javascript">sf_initialize()</script>
			<?php
				}
			?>
			<div class="clear"></div>
		</div>
			<div class="tab_content downloads" id="downloads_content">
				<?php foreach($list_download as $download) { ?>
					<div class="download_content">
						<?php
								if(!empty($download->field_download_url[0]['value'])) {
										$url = $download->field_download_url[0]['value'];
										$size = '';
								} else {
										$url = base_path() . $download->field_download_file[0]['filepath'];
										$size = '('.round($download->field_download_file[0]['filesize']/1048576,2).' mo)';
								}
								if(!empty($download->field_download_picto[0]['value'])) $picto =  base_path() . $download->field_download_picto[0]['value'];
								else $picto = $theme_images.'/'.$download->field_download_type[0]['value'];
						?>
						<img src="<?php echo $picto; ?>" />
						<a target="_blank" href="<?php echo $url; ?>"><?php echo $download->title; ?></a>
						<span class="size"><?php echo $size;?></span>
					</div>
				<?php } ?>

				<?php if (count($list_archived_download) > 0) { ?>
					<div class="archive_button">
						<a class="button_view" href="javascript: void(0);" title="<?php echo t('archive'); ?> <?php echo $title; ?>">
							<?php echo t('archive'); ?>
						</a>
					</div>
					<div class="archive_files">
						<?php foreach($list_archived_download as $download) { ?>
							<div class="download_content">
								<?php
										if(!empty($download->field_download_url[0]['value'])) {
												$url = $download->field_download_url[0]['value'];
												$size = '';
										} else {
												$url = base_path() . $download->field_download_file[0]['filepath'];
												$size = '('.round($download->field_download_file[0]['filesize']/1048576,2).' mo)';
										}
										if(!empty($download->field_download_picto[0]['value'])) $picto =  base_path() . $download->field_download_picto[0]['value'];
										else $picto = $theme_images.'/'.$download->field_download_type[0]['value'];
								?>
								<img src="<?php echo $picto; ?>" />
								<a target="_blank" href="<?php echo $url; ?>"><?php echo $download->title; ?></a>
								<span class="size"><?php echo $size;?></span>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
            <div class="clear"></div>
	</div>

	<div id="altpopdisplayer" style="position:absolute;z-index: 20002;" ></div>
