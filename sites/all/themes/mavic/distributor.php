<?php
	$result = db_query('select * from {node} INNER JOIN {content_type_distributor} using (vid) 
											 INNER JOIN {content_field_country_distrib} using (vid)
											 LEFT JOIN {content_field_website} using (vid)
											 LEFT JOIN {content_field_premium} using (vid)
											 LEFT JOIN {content_field_mp3} using (vid)
											 LEFT JOIN {content_field_rims} using (vid)
											 LEFT JOIN {content_field_tyres} using (vid)
											 LEFT JOIN {content_field_computers} using (vid)
											 LEFT JOIN {content_field_email} using (vid)
											 LEFT JOIN {content_field_pedals} using (vid)
											 LEFT JOIN {content_field_footwear} using (vid)
											 LEFT JOIN {content_field_apparel} using (vid)
											 LEFT JOIN {content_field_accessories} using (vid)
											 LEFT JOIN {content_field_mavic_lab} using (vid)
											 LEFT JOIN {content_field_tech_dealer} using (vid)
											 LEFT JOIN {content_field_wheels} using (vid)
											 LEFT JOIN {content_field_filtre_deux} using (vid)
											');
	$listDistribCountry = array();
	while ($row = db_fetch_object($result)) {
		if(!isset($listDistribCountry[$row->field_country_distrib_value])) 
		$listDistribCountry[$row->field_country_distrib_value] = array();
		$listDistribCountry[$row->field_country_distrib_value][] = $row;
	}
?>
<table id="distributor">
	<tr valign="top">
		<td>
			<h1><?php echo t('Europe') ?></h1>
			<?php displayDist(array('Belgium','Bulgaria','Croatia','Cyprus','Czech Republic','Denmark','Estonia','Finland','Greece','Italy','Hungary','Latvia','Lithuania','Luxembourg','Malta','Netherlands','Norway','Poland','Portugal','Serbia','Slovenia','Spain','Sweden','Turkey','Ukraine'), $listDistribCountry) ?>
		</td>
		<td>
			<h1><?php echo t('Middle East') ?></h1>
			<?php displayDist(array('Bahrain','Iran','Israel','Kuwait','Oman','Qatar','Saudi Arabia','United Arab Emirates'), $listDistribCountry) ?>
			<h1><?php echo t('Africa') ?></h1>
			<?php displayDist(array('Namibia','South Africa'), $listDistribCountry) ?>
			<h1><?php echo t('Asia') ?></h1>
			<?php displayDist(array('China','Hong Kong S.A.R., China','Cambodia','India','South Korea','North Korea','Philippines','Russia','Singapore','Taiwan','Thailand'), $listDistribCountry) ?>
		</td>
		<td>
			<h1><?php echo t('North America') ?></h1>
			<?php displayDist(array('Canada'), $listDistribCountry) ?>
			<h1><?php echo t('South America') ?></h1>
			<?php displayDist(array('Argentina','Brazil','Chile','Colombia','Costa rica','Ecuador','Guatemala','Mexico','Panama','Paraguay','Peru','Uruguay','Venezuela'), $listDistribCountry) ?>
			<h1><?php echo t('Oceania') ?></h1>
			<?php displayDist(array('Australia','New Zealand'), $listDistribCountry) ?>
		</td>
	</tr>
</table>
<br />
<br />
<br />
<?php
	function displayDist($listCountry, $listDistribCountry) {
		foreach($listCountry as $country) {
			$js_country = str_replace(array(' ',',','.'),'',$country);
			if (!empty($listDistribCountry[$country])) {
?>
			<div id="<?php echo $js_country ?>" class="country" onclick="display_country_dist('#<?php echo $js_country ?>')" >
				<?php echo t($country) ?>
				<img class="img_distri" src="<?php echo base_path().path_to_theme();?>/images/asc.gif" />
			</div>
			<div id="<?php echo $js_country ?>_dist" style="display:none" class="country_dist" >
				<?php 
					foreach($listDistribCountry[$country] as $dist) {
				?>
						<div class="infoBox">
							<h2><?php echo $dist->title?></h2>
							<div class="infoBoxContent">
								<?php echo $dist->field_street_value ?><br />
								<?php echo $dist->field_postal_code_value.' '.$dist->field_city_value ?><br />
								<?php echo t($dist->field_country_name_value) ?><br />
								<?php echo t('phone').' : '.$dist->field_phone_value ?><br />
								<?php if(!empty($dist->field_email_email)) echo $dist->field_email_email.'<br />' ?>
								<?php
									$products = array();
									if($dist->field_wheels_value) $products[] = t('Wheels');
									if($dist->field_rims_value) $products[] = t('Rims');
									if($dist->field_tyres_value) $products[] = t('Tyres');
									if($dist->field_computers_value) $products[] = t('Computers');
									if($dist->field_pedals_value) $products[] = t('Pedals');
									if($dist->field_footwear_value) $products[] = t('Footwear');
									if($dist->field_filtre_deux_value) $products[] = t('Helmets');
									if($dist->field_apparel_value) $products[] = t('Apparel');
									if($dist->field_accessories_value) $products[] = t('Accessories');
									if(!empty($products)) {
										echo '<b>'.t('Products') . ' : </b>';
										echo implode(' - ',$products).'<br />';
									}
									$services = array();
									if($dist->field_premium_value == 'mavic_yellow') $services[] = t('PREMIUM+');
									if($dist->field_mp3_value) $services[] = t('MP3');
									if($dist->field_mavic_lab_value) $services[] = t('MAVIC LAB');
									if($dist->field_tech_dealer_value) $services[] = t('TECH DEALER');
									if(!empty($services)) {
										echo '<b>'.t('Services') . ' : </b>';
										echo implode(' - ',$services).'<br />';
									}
								?>
								<br />
							</div>
						</div>
						
				<?php
						}
					}
				?>
			</div>
<?php
		}
	}
?>
<script type="text/javascript">
	function display_country_dist(country) {
		if ( typeof this.current != 'undefined' && this.current != "") { // var static
			$(this.current+'_dist').slideUp("400",function() {positionLogo()});
			$(this.current+' .img_distri').hide();
			$(this.current).removeClass('country_selected');
			if (this.current == country) {
				this.current = "";
				return;
			}
		}
		this.current = country;
		$(country+'_dist').slideDown("400",function() {positionLogo()});
		$(country+' .img_distri').show();
		$(country).addClass('country_selected');
	}
</script>
	
