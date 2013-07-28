<script type="text/javascript" >

$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		$('#main_content').css({'background-color':'transparent', 'padding':0});
	});
</script>
<div id="tabs" class="midlevel">


	<?php
	function getLevel2HtmlPage($level2Items, $field_nav_desc)
	{
		$total = count($level2Items);
		$i = 1;
		foreach ($level2Items as $item) :
	
		$classW = explode('_',$item->localized_options['attributes']['title']);
		if(!(($i%4==0)&&($classW[0] == 'wheels')))
		{
			($i > 3) ? $class2 = " apparel" : $class2 = '';
			$out.= '
						<a class="link '.$classW[0].$class2.'" href="'.url($item->href, $item->localized_options).'" >
							<img src="'.base_path().path_to_theme().$item->img_src.'" alt="" />
							<span class="helvetica navtitle" >'.$item->title.'</span>';
			if (isset($field_nav_desc[$i-1]['value'])) $out.= '<p class="navdesc">'. $field_nav_desc[$i-1]['value'].'</p>';
			$out.= '		</a>';
		}
		$i++;
		endforeach;
		$out.= '<div class="clear"></div>';
		return $out;
	}
	foreach ($primary_links as $data) {
		$level2Items = array();
		if($data['link']['title'] == $title) {
			$output='<div id="submenu_productsItem" class="submenu_page">';
			foreach ($data['below'] as $j => $subdata)
			{
				$subsubElem = @reset($subdata['below']);
				$subsubNext = @next($subdata['below']);
				$level2Item = new stdClass();
				$level2Item->title = $subdata['link']['title'];
				$level2Item->img_src = '/images/menus/'.$subdata['link']['options']['attributes']['title'].".jpg";
				$level2Item->href = $subsubElem['link']['href'];
				$level2Item->localized_options = $subdata['link']['localized_options'];
				$level2Item->cat_title = $subsubNext['link']['title'];
				$level2Item->cat_href = $subsubNext['link']['href'];
				$level2Items[] = $level2Item;
			}
			$output .= getLevel2HtmlPage($level2Items, $field_nav_desc);
			$output .= '</div>';
			$output .= '<script>
						$(document).ready(function() {
								var originalHeight =  $("#submenu_productsItem").height();
								function changePageHeight() {
										var newHeight = ($("#logo_container").offset().top + $("#logo_container").height()) - $("#submenu_productsItem").offset().top;
										if($("#submenu_productsItem").height() < newHeight || (newHeight >= originalHeight && newHeight < $("#submenu_productsItem").height())) $("#submenu_productsItem").css("height", newHeight);
								}

								$(window).bind("resize", function () {changePageHeight()});
								changePageHeight();
						});
				</script>';
			break;
			
		}
		$i++;
	}
	print $output;
	
	
	?>
	</div>