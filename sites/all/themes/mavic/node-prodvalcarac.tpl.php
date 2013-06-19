<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
</script>
<div id="tabs">
<?php
				$a_translat = array();
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
				$mtb_translat = array(
					"en" => "mountain-bike",
					"fr" => "VTT",
					"de" => "MTB",
					"es" => "MTB",
					"it" => "mountain-bike",
					"ja" => "MTB"
				);
			switch ($discipline) {
				case 'road':
					$a_translat = $road_translat;
					break;
				case 'triathlon':
					$a_translat = $tria_translat;
					break;
				case 'mtb':
					$a_translat = $mtb_translat;
					break;
			}
			$fg = 0;
			$onRJP = false;//true si sur tab roue jantes 
			$onT = false;//true si sur textile
			$onC = false;//true si sur chaussure

			if($menu_technologies){
				foreach($menu_technologies as $technotab){
					$fg++;
					if($fg < 3  && $technotab['link']['mlid'] == $breadcrumb[1]['link']['mlid']) $onRJP = true;
					if($fg == 7  && $technotab['link']['mlid'] == $breadcrumb[1]['link']['mlid']) $onC = true;
					if($fg == 8  && $technotab['link']['mlid'] == $breadcrumb[1]['link']['mlid']) $onT = true;
				}
			}
	require_once('node-technoline-tabs.tpl.php');
	$depth = count($breadcrumb);
	if($depth == 4) {
?>
		<ul id="techno_list_cat_small">
			<?php
				$i = 0;
				foreach($breadcrumb[1]['below'] as $product) {
					$item = menu_get_item($product['link']['href']);
					$itemMap = $item['map'][1];
					$child = reset($product['below']);
					$link = url($child['link']['href']);
					if($product['link']['in_active_trail']) $classe = 'current_tech_cat';
					else $classe = '';
					if($i == 1) {
						$i = 0;
						$classe_li = ' second_li_cat';
					} else {
						$classe_li = '';
						$i++;
					}
			?>
					<li class="li_cat<?php echo $classe_li; ?>">
						<a href="<?php echo $link; ?>" class="<?php echo $classe; ?>" >
							<img border="0" class="img_cat" src="<?php echo $technologies_path.'/categories/small/'.$itemMap->body.'.jpg'; ?>" alt="<?php echo $itemMap->title; ?>" align="left" />
							<h1 class="helvetica"><?php echo $itemMap->title ?></h1>
							<div class="more"><?php echo t('See all @techno technologies',array('@techno'=>$itemMap->title)); ?></div>
							<div class="clear" /></div>
						</a>
						<?php if($product['link']['in_active_trail']) echo '<img src="'.$theme_images.'/techno_down_arrow.gif" align="top" border="0"/>'; ?>
					</li>
			<?php
				}
			?>
		</ul>
		<div class="clear"></div>
<?php
	}
?>

	<h2 id="techno_title">
		<?php 
		//remonte la bonne image
		$featureLogoCodeFound = '';
		foreach($field_feature_codes as $featureCodeValue){
			if(file_exists($technologies_path_sys.'/logos/'.$featureCodeValue["value"].'.jpg') ){ ?>
			<img id="techno_logo" src="<?php 
				$featureLogoCodeFound = $featureCodeValue['value'];
				echo $technologies_path.'/logos/'.$featureLogoCodeFound.'.jpg';?>" alt="<?php echo $title; ?>" />
		<?php 
				
			
				break;
			} 
		}?>
		<span class="helvetica"><?php echo $title; ?></span>
		<?php if(!empty($field_site_name[0]['value']) & !empty($field_site_link[0]['value'])) { ?>
			<a class="button_view" href="<?php echo url($field_site_link[0]['value'])?>" target="<?php echo $field_site_target[0]['value']?>"><?php echo $field_site_name[0]['value'] ?></a>
		<?php } ?>
	</h2>
	<div id="techno_img_full">
		<?php
		foreach($field_feature_codes as $featureCodeValue){
		
				if( file_exists($technologies_path_sys.'/full/'.$featureCodeValue['value'].'.swf') ){ ?>
			<script language="javascript">
				var parameters =
				{     width: "710"
				    , height: "308"
					, language: "<?php echo $lang; ?>"
					, wmode: "opaque"
				};

				// Embed the player SWF:
				swfobject.embedSWF(
					  "<?php 
					  echo $technologies_path.'/full/'.$featureCodeValue['value'].'.swf'; ?>"
					, "playerTechno"
					, parameters["width"], parameters["height"]
					, "10.0.0"
					, {}
					, parameters
					, { allowFullScreen: "true", wmode: "opaque" }
					, { name: "StrobeMediaPlayback" }
				);
			</script>
			<div id="playerTechno"></div>
		<?php 
			break;	} else { 
			if( file_exists($technologies_path_sys.'/full/'.$featureCodeValue['value'].'.jpg') ){
				?>
							<img src="<?php 
			echo $technologies_path.'/full/'.$featureCodeValue['value'].'.jpg';?>" alt="<?php echo $title; ?>" />
		<?php break;
				} 
			}
		}?>
	</div>
	<div id="techno_content">
<?php
		if(empty($field_child[0]['nid'])) {
?>
			<ul id="techno_assoc">
				<h5><?php echo t('Associated products:'); ?></h5>
<?php
// cette requete prend en compte la valeur du title selon la derni�re r�vision mais a priori cette valeur est celle qui est d�j� dans node
// la requete est donc simplifi�e
//				$macro_nid_tab = db_query('SELECT nr.nid, nr.title FROM content_field_technologienode o INNER JOIN node_revisions nr USING (vid) INNER JOIN node n ON n.nid=nr.nid and n.vid=nr.vid, menu_links m WHERE o.field_technologienode_nid='.$nid.' and m.link_path=concat("node/", o.nid) and n.status=1 order by m.weight');
				$macro_nid_tab = db_query('SELECT n.nid, n.title FROM content_field_technologienode o INNER JOIN node n USING (nid,vid), menu_links m WHERE o.field_technologienode_nid='.$nid.' and m.link_path=concat("node/", o.nid) and n.status=1 and n.type="macromodel" order by m.weight');
				
				while($prod = db_fetch_array($macro_nid_tab)) {
					//si dans un sous-domaine et sur textil,  renvoie sur main-domain.
					($onT || empty($discipline))? $href = url('node/'.$prod['nid'], array('absolute'=>'true','base_url'=>'http://www.mavic.com')) : $href = url('node/'.$prod['nid']);
					//vérifie que les produit associé fassent bien parties du sous-domaine
					$isDisc = false;
					if (strpos($href, $a_translat[$lang])) $isDisc = true;					
					if ($isDisc || empty($discipline) || (!$onRJP && !$onC)) {
?>
					<li><a href="<?php echo $href;?>"><?php echo $prod['title']?></a></li>
<?php
					}
				}
?>
			</ul>
<?php
		}
?>
		<div id="techno_content_text">
			<?php echo $node->content['body']['#value']; ?>
			<?php if(!empty($field_consoarglb[0]['value'])) { ?>
				<br>
				<ul id="techno_conso">
					<?php
						foreach ($field_consoarglb as $conso) {
							echo '<li>'.$conso['value'].'</li>';
						}
					?>
				</ul>
			<?php } ?>
		</div>
	
		<br class=clear />
	</div>
<?php
	if(!empty($field_child[0]['nid'])) {
		echo '<h3 class="helvetica" id="child_techno_title">'.t('Technical versions').'</h3>';
?>
		<ul id="child_techno_ul">
<?php
			foreach($field_child as $child) {
				//echo $child['view'];
				$childItem = node_load($child['nid']);
				//remonte la bonne image
				$childFeatureCodeFound = '';
				foreach($childItem->field_feature_codes as $ChildFeatureCodeValue){
					if( file_exists( $technologies_path_sys.'/children/'.$ChildFeatureCodeValue["value"].'.jpg')){
						$childFeatureCodeFound = $ChildFeatureCodeValue['value'];
						break;
					} 
				}
				
				$assocPro = '';
				$macro_nid_tab = db_query('SELECT n.nid, n.title FROM content_field_technologienode o INNER JOIN node_revisions n USING (vid), menu_links m WHERE o.field_technologienode_nid='.$childItem->nid.' and m.link_path=concat("node/", o.nid) order by m.weight');
				while($prod = db_fetch_array($macro_nid_tab)) {
					//si dans un sous-domaine et sur textil,  renvoie sur main-domain.
					($onT || empty($discipline))? $href = url('node/'.$prod['nid'], array('absolute'=>'true','base_url'=>'http://www.mavic.com')) : $href = url('node/'.$prod['nid']);
					//vérifie que les produit associé fassent bien parties du sous-domaine
					$isDisc = false;
					if (strpos($href, $a_translat[$lang])) $isDisc = true;	
					if ($isDisc || empty($discipline) || $onT) {
						$assocPro .= '<li><a href="'.$href.'">'.$prod['title'].'</a></li>';
					}
				}
				
			if (!empty($assocPro)){
?>		

<li class="li_child_techno">
	<img src="<?php echo $technologies_path.'/children/'.$childFeatureCodeFound.'.jpg'; ?>" />
	<ul>
		<h5><?php echo t('Associated products:'); ?></h5>
		<?php
			echo $assocPro;
		?>
	</ul>
	<div class="child_techno_content">
		<h4 class="helvetica"><?php echo $childItem->title; ?></h4>
		<?php echo $childItem->body; ?>
	</div>
	<div class="clear">&nbsp;</div>
</li>
<?php			
}
}
?>
		</ul>
<?php
	}
	$parent = $depth - 2;
	$elem = $depth - 1;
	$current = $breadcrumb[$elem]['key_breadcrumb'];
	if(count($breadcrumb[$parent]['below']) > 1) {
		reset($breadcrumb[$parent]['below']);
		$previous = '';
		while(key($breadcrumb[$parent]['below']) != $current) {
			$previous = current($breadcrumb[$parent]['below']);
			next($breadcrumb[$parent]['below']);
		}
		$next = next($breadcrumb[$parent]['below']);
?>
		<div id="techno_prev_next">
			<?php
				if(!empty($previous) && empty($discipline)) {
					echo '<a class="button_view button-view-previous" href="'.url($previous['link']['href']).'">'.t('PREVIOUS TECHNOLOGY').'</a>';
				}
				if($next && empty($discipline)) {
					echo ' <a class="button_view button-view-next" href="'.url($next['link']['href']).'">'.t('NEXT TECHNOLOGY').'</a>';
				}
			?>
		</div>
<?php
		$i = 0;
		foreach($breadcrumb[$parent]['below'] as $product) {
			$item = menu_get_item($product['link']['href']);
			$itemMap = $item['map'][1];
			//remonte la bonne image
			$brotherFeatureCodeFound = '';
			foreach($itemMap->field_feature_codes as $brotherFeatureCodeValue){
				if( file_exists( $technologies_path_sys.'/brother/'.$brotherFeatureCodeValue["value"].'.jpg')){
					$brotherFeatureCodeFound = $brotherFeatureCodeValue['value'];
					break;
				}
			}
			
			$isDisc = false;
			if (!empty($discipline)){
				$macro_assoc = db_query('SELECT n.nid FROM content_field_technologienode o INNER JOIN node n USING (nid,vid), menu_links m WHERE o.field_technologienode_nid='.$itemMap->nid.' and m.link_path=concat("node/", o.nid) and n.status=1 and n.type="macromodel" order by m.weight');
				while ($assocP = db_fetch_array($macro_assoc)){
					$dischref = url('node/'.$assocP['nid']);
					if (strpos($dischref, $a_translat[$lang])) $isDisc = true;					
				}
			}
			if ($isDisc || empty($discipline) || !$onRJP) {
				$link = url($product['link']['href']);
				if($product['link']['in_active_trail']) $classe = ' techno_brother_actif'; else $classe = '';
				if($i == 2) {
					$classe .= ' third_brother_techno';
					$i = 0;
				} else {
					$i++;
				}
			
?>
			<a href="<?php echo $link?>" class="techno_brother<?php echo $classe ?>">
				<img src="<?php echo $technologies_path.'/brother/'.$brotherFeatureCodeFound?>.jpg" />
				<div class="helvetica techno_brother_title"><?php echo mb_strtoupper($itemMap->title) ?></div>
			</a>
<?php
				if( $i == 0 ){
?>
				<div class="clear"></div>
<?php
				}
			}
		}
	}
?>
</div>
<div class="clear"></div>


