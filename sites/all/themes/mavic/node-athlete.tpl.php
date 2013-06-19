<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
</script>
<script src="<?php echo base_path().path_to_theme();?>/js/api_flickr.js"></script>

<div id="tabs">

<?php
	if (empty($discipline)) require_once('node-athlete_cat-tabs.tpl.php');
	$depth = count($breadcrumb);
	
?>

	<div  class="athletes">
	
		<img id="img_athlete" align="left" alt="<?php echo $title;?>" src="<?php echo base_path(). $field_athlete_photo[0]['filepath'] ?>" />
		<h1 class="helvetica">
			<?php echo $title;?>
			<?php 
				if(!empty($field_team_name[0]['value']))
					echo '<br />('.$field_team_name[0]['value'].')';
			?>
		</h1>
		<span id="info_athlete">
<?php
			if(!empty($field_athlete_product[0]['nid']) && db_fetch_object(db_query("SELECT nid, title, created FROM {node} WHERE nid = %d ", $field_athlete_product[0]['nid']))) {
				echo '<b>'.t('product used:').'</b>';
				echo '<table cellspacing=0 cellpadding=0 valign="top"><tr valign="top"><td><ul>';
				foreach($field_athlete_product as $key=>$product) {
				// test si le produit n'est pas vide // ne suffit pas : doit tester de l'existence de ce produit !!!
					if(!empty($product['nid']) && db_fetch_object(db_query("SELECT nid, title, created FROM {node} WHERE nid = %d ", $product['nid']))){
						if($key%4 == 0 && $key!=0) echo '</ul></td><td><ul>';
						echo '<li><a href="'.url('node/'.$product['nid']).'">'.$product['safe']['title'].'</a></li>';
					}
				}
				echo '</ul></td</tr></table>';
			}
?>
		</span>
		<div class="clear"></div>
		<!-- thumbnail -->
		<h2 class="helvetica" id="gallerytitle"><?php echo t('photo gallery'); ?></h2>
		<div id="gallery"></div>
<?php
		$tags = '';
		foreach($node->field_flickr_ref as $tag) {
			$tags .= $tag['value'].',';
		}
		$tags = substr($tags,0,-1);
?>
		<!-- load data from flickr for gallery and lightbox-->
		<script type="text/javascript">
			<?php if($tags=='') { ?>$("#gallery,#gallerytitle").css({display:'none'});<?php } ?>
			flickr_gallery.getTags('<?php echo $tags; ?>',display_set);
		</script>
<?php
		
		//
		// gestion news associes
		//
		$list_news = array();
		$query = 'select distinct n.nid from content_field_news_athlete c INNER JOIN node n using (nid) INNER JOIN content_type_news t using (nid) '.
						'where c.field_news_athlete_nid="'.$nid.'" and n.status=1 and n.language="'.$lang.'"'.
						'order by t.field_news_date_value desc';
		$res = db_query($query);

		while ($news_nid = db_fetch_array($res)) {
			$list_news[] = node_load($news_nid['nid']);
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
		
		if(!empty($list_news)) {
?>
			<h2 class="helvetica"><?php echo t('news'); ?></h2>
			
			<div id="relatednews_content" class="tab_content relatednews">
<?php 
				foreach($list_news as $news) { 
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
								<a href="<?php echo url($news->path, array('absolute' => true)) ?>"class="moreinfos"><?php echo t('More infos') ?></a>
							</p>
						</div>
						<div class="clear"></div>
					</div>
<?php 
				} 
?>
			</div>
<?php 
		}
		
		if(!empty($field_palmares[0]['value'])) {
			if(!empty($list_news)) {
?>
				<h2 class="helvetica" style="margin-top:5px"><?php echo t('career highlights'); ?></h2>
<?php
			} else {
?>
				<h2 class="helvetica"><?php echo t('career highlights'); ?></h2>
<?php
			}
?>
			
			<div class="tab_content downloads" id="downloads_content">
				<?php foreach($field_palmares as $palmares) { ?>
					<div class="download_content">
						<?php echo $palmares['value']; ?>
					</div>
				<?php } ?>
			</div>
<?php 
		} 
?>

	</div>
</div>