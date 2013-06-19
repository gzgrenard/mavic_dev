<?php 
		//
		$query = 	'SELECT country
					FROM location_instance
					INNER JOIN node
					USING ( nid, vid )
					INNER JOIN node_revisions
					USING ( vid )
					INNER JOIN content_type_shop
					USING ( vid )
					INNER JOIN location ON location.lid = location_instance.lid
					WHERE field_shopinshop_value =1
					ORDER BY country ASC';
					
		$result = db_query( $query );
		$currentCountry = '';
		//$countries = array();
		$countryList = '<ul id="list_select_countries">';
		$contriesName = sfGetCountries();
		while(($resultArray = db_fetch_array($result)) !== false){
			foreach($resultArray as $key => $value){
				if($currentCountry != $value){
					//$countries[$value] = $contriesName[$value];
					$countryList .='<li><a href="#" onclick="$(\'#countrySelect\').get(0).value=\''.$value.'\';$(\'#selected_country\').html(\''.$contriesName[$value].'\');searchLocations(\''.$value.', '.$contriesName[$value].'\');">'.$contriesName[$value].'</a></li>';
					$currentCountry = $value;
				}
			}

		}
		$countryList .= '</ul>';
		
		


?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var map;
    var geocoder;
    var markersArray = [];
    var circle = null;
    var currentDatas;
    var fInfoBox;
    var panes = null;
    
	//grid factors for shop grouping
    var factor = [];

    factor[1]= factor[2]=0.000001;
    factor[3]=0.03;
    factor[4]=0.15;
    factor[5]=0.3;
    factor[6]=0.75;
    factor[7]=1.5;//2
	factor[8]=3;
	factor[9]=10;
	factor[10]=25;
	factor[11]=50;
	factor[12]=factor[13]=factor[14]=factor[15]=factor[16]=factor[17]=factor[18]=factor[19]=factor[20]=factor[21]=100000;
	
	//
	// ajax request to take visitor IP and location without drupal cache
	//
	function getVisitorLocation() { 
		var latLong; 
		$.ajax({
			url: "/shopfinder_my_ip.php", 
			async: false, 
			dataType: "json",
			success: function(data) { 
			
			  latLong = data;
			},
			error: function() { 
			  latLong = {"country_code":"fr"}; 
			}
		})
		return latLong; 
	}

    function sis_initialize() {
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(46.227638, 2.213749);
        var myOptions = {
          zoom: 2,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          scrollwheel: false
        }
        map = new google.maps.Map(document.getElementById("map"), myOptions);  
        google.maps.event.addListener(map, 'zoom_changed', function() {
        	drawClusterMarkers();
        	
        });

        fInfoBox = new InfoBox({ map: map });

        $('#body').click( function(){ $('#list_select_countries').css('display','none'); });
        
		var latLong = getVisitorLocation();
		if(latLong.country_code != 0) {
			$(document).ready(function() {
				document.getElementById('countrySelect').value = latLong.country_code;
			});
			searchLocations(latLong.country_code);
		} 
    }
	
	function searchLocations(param) {
		
		var country = document.getElementById('countrySelect').value;
		if(param != undefined) { var countryName = param } else { var countryName = country }; // countrySelect field can be not initialized yet
		geocoder.geocode( { 'address': countryName, 'region': country}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				searchLocationsNear(results[0].geometry.location, results[0].geometry.viewport);
				//console.log('baorasd  : ' + results);
				//console.log(results);
			} else {
					document.getElementById('shopresults').innerHTML = '<div  class="noresult"><?php echo t('No results found.'); ?></div>';
					document.getElementById('scroller_button_left').style.visibility = 'hidden';
					document.getElementById('scroller_button_right').style.visibility = 'hidden';
			}
		});
	}

	function searchLocationsNear(center, bounds){
		var selectedCountry = document.getElementById('countrySelect').value;
		var searchUrl = '/storefinder/find/1/0/0/' + selectedCountry;
		fInfoBox.hide();
		
		if($('#bullet_bike_sytem').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet_riders_equip').hasClass('checked'))searchUrl += '/1/0/0/0/0/0/0'
		else searchUrl  += '/0/0/0/0/0/0/0';
		//console.log(searchUrl);
		downloadUrl(searchUrl, function(data) {
			currentDatas = data;
			//recenter map
			map.setCenter(center);
			map.fitBounds(bounds);
			
			map.setZoom(map.getZoom());//drawClusterMarkers(); marker draw is called during zoom thrue event listener
			
			});
	}

		function drawClusterMarkers()
		{
			deleteOverlays();
			
			var xmlMarkers = currentDatas.documentElement.getElementsByTagName('marker');
			//get back the initial datas
			data = currentDatas;
			
			//remove previous search
			deleteOverlays();
			//no markers
			if (xmlMarkers.length == 0) 
			{
		    	document.getElementById('shopresults').innerHTML = '<div class="noresult"><?php echo t('No results found.'); ?></div>';
				document.getElementById('scroller_button_left').style.visibility = 'hidden';
				document.getElementById('scroller_button_right').style.visibility = 'hidden';
				return;
		    }

			/****group markers*****/
			//generate grid
			grid = [];
			gridResult = [];
		
			f = map.getZoom();
			v=factor[f];
			
			scount = 1;
			if (xmlMarkers.length > 3) {
				document.getElementById('scroller_button_left').style.visibility = 'hidden';
				document.getElementById('scroller_button_right').style.visibility = 'visible';
			} else {
				document.getElementById('scroller_button_left').style.visibility = 'hidden';
				document.getElementById('scroller_button_right').style.visibility = 'hidden';
			}
			for ( i = 0; i < xmlMarkers.length; i++) {

				x = Math.round( xmlMarkers[i].getAttribute('lng')*v )/v ;
				y = Math.round( xmlMarkers[i].getAttribute('lat')*v )/v ;
		
				if(!grid[x])grid[x]=[];
				if(!grid[x][y])
					{
						grid[x][y]=[];
						grid[x][y]['markers']=[];
					}
				//position marker on the grid
				grid[x][y]['markers'][grid[x][y]['markers'].length]= xmlMarkers[i];

				//create marker in the shopresults
				var div = document.createElement('div');
				div.className = 'shopitem';
				var html = '';
				html += '<div class="title" >'+scount+' '+xmlMarkers[i].getAttribute('name')+'</div> ';
				html += '<div class="body">';
				html += xmlMarkers[i].getAttribute('postal_code') + ' ' + xmlMarkers[i].getAttribute('city')+ '<br/>';
				html += xmlMarkers[i].getAttribute('countryname')+ '<br/>' ;
				var phone = xmlMarkers[i].getAttribute('phone');
				if(phone != ''){html += '<?php echo t('phone')?> : ' + xmlMarkers[i].getAttribute('phone')+ '<br/>' ;};

				
				html += '<b><a class="getdirection" target="_blank" href="http://maps.google.com/maps?daddr='+xmlMarkers[i].getAttribute('street')+' '+xmlMarkers[i].getAttribute('postal_code')+' '+xmlMarkers[i].getAttribute('city')+' '+xmlMarkers[i].getAttribute('countryname')+'"><?php echo t('Get Direction ')?></a></b>';
				
				html += '</div>';
				html += '</div>';
				div.markerId = 'group'+x+y;
				div.innerHTML = html;
				div.xmlMarker = xmlMarkers[i];
				google.maps.event.addDomListener(div, 'click', 
					function() {
						fInfoBox.latlng_ = new google.maps.LatLng(parseFloat(this.xmlMarker.getAttribute('lat')),parseFloat(this.xmlMarker.getAttribute('lng')));
						fInfoBox.xmlFields = this.xmlMarker;
						fInfoBox.fillContent();
						fInfoBox.draw();
						fInfoBox.show();
						fInfoBox.panMap();
					});
				document.getElementById('shopresults').appendChild(div);
				scount++;
				
			}

			//
			for( x in grid )
			{
				for( y in grid[x] )
				{
					n = grid[x][y]['markers'].length;
					if( n > 1 )
					{
						//calculate baricentre with all markers and store only 1 marker for xml sending
						sumLat = 0;
						sumLng = 0;
						
						for( i in grid[x][y]['markers']  )
						{
							sumLat += parseFloat(grid[x][y]['markers'][i].getAttribute('lat'));
							sumLng += parseFloat(grid[x][y]['markers'][i].getAttribute('lng'));
						}
						
						lat = sumLat/n;
						lng = sumLng/n;
		
						mGroup = data.createElement('marker');
						mGroup.setAttribute('isGroup',true);
						mGroup.setAttribute('lat',lat);
						mGroup.setAttribute('lng',lng);
						mGroup.setAttribute('field_premium_value','mavic_yellow');
						mGroup.setAttribute('number',n);
						mGroup.setAttribute('nid','group'+x+y);
						gridResult[gridResult.length] = mGroup;
					}
					else
					{
						grid[x][y]['markers'][0].setAttribute('nid','group'+x+y);
						gridResult[gridResult.length] = grid[x][y]['markers'][0];
					}
				}
			}
			/**********************/
		    //treat all grid entries
		    
		    for ( i = 0; i < gridResult.length; i++) {

			    var xmlFields = gridResult[i];
			    var position = new google.maps.LatLng(parseFloat(gridResult[i].getAttribute('lat')),parseFloat(gridResult[i].getAttribute('lng')));

			    
				if( gridResult[i].getAttribute('isGroup')  )
				{
					//create group marker on the map
				    marker = createGroupMarker( position , xmlFields);
				}
				else
				{
					//create marker on the map
				    marker = createMarker( position , xmlFields);
				}
		
				
			    //store marker in the markersarray
			    markersArray[gridResult[i].getAttribute('nid')]=marker;
		    }
		    //qfont
		    Cufon.replace('div.helvetica', {hover: true, ignore: { ul: true }, "font-family": "Helvetica75"});
		    
		}
		  
	
		
		//create a marker
		function createMarker(position, xmlFields)
		{
			marker = new google.maps.Marker({
			    position: position,
			    map: map
			});
			//add custom infowindow (infobox) for the marker			
			google.maps.event.addListener(marker, 'click', function() {
				fInfoBox.latlng_ = this.getPosition();
				fInfoBox.xmlFields = xmlFields;
				fInfoBox.fillContent();
				fInfoBox.draw();
				fInfoBox.show();
				fInfoBox.panMap();
			});

			//add specific icon
			marker.setIcon('/sites/default/themes/mavic/images/gmap_'+xmlFields.getAttribute('field_premium_value')+'.png');
			return marker;
		}

		//create 1 marker for a group of shops
		function createGroupMarker(position, xmlFields)
		{
			marker = new google.maps.Marker({
			    position: position,
			    map: map
			});
			//add number for the marker			
			
			marker.text = new TextMarker({latlng: position, map: map, xmlFields: xmlFields});

			//add custom infowindow (infobox) for the marker			
			google.maps.event.addListener(marker, 'click', function() {
				map.setCenter(this.position);
				map.setZoom(map.getZoom()+1);
			});
			
			//add specific icon
			marker.setIcon('/sites/default/themes/mavic/images/gmap_mavic_white.png');
			
			return marker;
		}

		// Removes the overlays from the map, but keeps them in the array
		function clearOverlays() {
		  if (markersArray) {
		    for (i in markersArray) {
		    	markersArray[i].setMap(null);
		    }
		  }
		  
		}

		// Shows any overlays currently in the array
		function showOverlays() {
			alert(markersArray);
		  if (markersArray) {
			  
		    for (i in markersArray) {
			   alert(i);
		      markersArray[i].setMap(map);
		    }
		  }
		}

		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
		  if (markersArray) {
		    for (i in markersArray) {
		      markersArray[i].setMap(null);
		      if(markersArray[i].infobox)markersArray[i].infobox.setMap(null);
		      if(markersArray[i].text)markersArray[i].text.setMap(null);
		    }
		    markersArray.length = 0;
		  }
		  document.getElementById('shopresults').innerHTML='';
		  $('#shopresults').css('margin-left',0);
		  document.getElementById('scroller_button_left').style.visibility = 'hidden';
		  document.getElementById('scroller_button_right').style.visibility = 'hidden';
		}



	/*************************************************************
	custom infowindow infobox class
	**************************************************************/
	/* An InfoBox is like an info window, but it displays
	 * under the marker, opens quicker, and has flexible styling.
	 * @param {GLatLng} latlng Point to place bar at
	 * @param {Map} map The map on which to display this InfoBox.
	 * @param {Object} opts Passes configuration options - content,
	 *   offsetVertical, offsetHorizontal, className, height, width
	 */
	function InfoBox(opts) {
	  google.maps.OverlayView.call(this);
	  this.latlng_ = opts.latlng;
	  this.map_ = opts.map;
	  this.xmlFields = opts.xmlFields;
	  this.offsetVertical_ = -285;
	  this.offsetHorizontal_ = -25;
	  this.height_ = 280;
	  this.width_ = 266;
	  this.display = true;

	  var me = this;
	  
	  // Once the properties of this OverlayView are initialized, set its map so
	  // that we can display it.  This will trigger calls to panes_changed and
	  // draw.
	  this.setMap(this.map_);
	  
		//create the div 
	    div = this.div_ = document.createElement("div");
	    div.className="infoBox";
	    
	}
	
	/* InfoBox extends GOverlay class from the Google Maps API
	 */
	InfoBox.prototype = new google.maps.OverlayView();

	/* remove the DIV representing this InfoBox
	 */
	InfoBox.prototype.remove = function() {
	  if (this.div_) {
	    this.div_.parentNode.removeChild(this.div_);
	  }
	};

	/* hidde the DIV representing this InfoBox
	 */
	InfoBox.prototype.hide = function() {
  	    this.div_.style.display='none';
  	    this.display = false;
	};

	/* show the DIV representing this InfoBox
	 */
	InfoBox.prototype.show = function() {
		hideInfoBoxes();
		
	    this.div_.style.display='block';
	    this.display = true;
	};

	/* show the DIV representing this InfoBox
	*/
	InfoBox.prototype.toggle = function() {
	  if (this.display) {
	    this.hide();
	  }
	  else
	  {
		  this.show();
		  this.panMap();
	  }
	};
	
	/* Redraw the Bar based on the current projection and zoom level
	 */
	InfoBox.prototype.draw = function() {
		  // Creates the element if it doesn't exist already.
		  this.createElement();
		  
		
	    
	    if (!this.div_ ) return;
	  // Calculate the DIV coordinates of two opposite corners of our bounds to
	  // get the size and position of our Bar
	  var pixPosition = this.getProjection().fromLatLngToDivPixel(this.latlng_);
	  if (!pixPosition) return;

	  // Now position our DIV based on the DIV coordinates of our bounds
	  this.div_.style.width = this.width_ + "px";
	  this.div_.style.left = (pixPosition.x + this.offsetHorizontal_) + "px";
	  this.div_.style.height = this.height_ + "px";
	  this.div_.style.top = (pixPosition.y + this.offsetVertical_) + "px";
	  if(this.display ) this.div_.style.display = 'block';
	  else this.div_.style.display = 'none';

	  var panes = this.getPanes();
	    panes.floatPane.appendChild(div);
	};

	/* Creates the DIV representing this InfoBox in the floatPane.  If the panes
	 * object, retrieved by calling getPanes, is null, remove the element from the
	 * DOM.  If the div exists, but its parent is not the floatPane, move the div
	 * to the new pane.
	 * Called from within draw.  Alternatively, this can be called specifically on
	 * a panes_changed event.
	 */
	InfoBox.prototype.createElement = function() {
	  
	  var div = this.div_;
	  if(!panes){return;}
	  if (!div) {
	    
	  } else if (div.parentNode != panes.floatPane) {
	    // The panes have changed.  Move the div.
	    div.parentNode.removeChild(div);
	    panes.floatPane.appendChild(div);
	  } else {
	    // The panes have not changed, so no need to create or move the div.
	  }
	}

	/*Write in the div element the information according to xmlFields*/
	InfoBox.prototype.fillContent = function()
	{
		if(this.xmlFields && this.div_){
			div = this.div_;
			div.innerHTML = '';
		    div.style.width = this.width_ + "px";
		    div.style.height = this.height_ + "px";
	
		    var titleDiv = document.createElement("h2");
		    titleDiv.innerHTML = this.xmlFields.getAttribute('name');
		    titleDiv.className = '';
		    
		    var contentDiv = document.createElement("div");
		    contentDiv.className='infoBoxContent';
			//content of the infobox
	
		    //contentDiv.innerHTML += '<br/>';
		    contentDiv.innerHTML += this.xmlFields.getAttribute('street');
		    contentDiv.innerHTML += '<br/>';
		    contentDiv.innerHTML += this.xmlFields.getAttribute('postal_code') + ' ' + this.xmlFields.getAttribute('city');
		    contentDiv.innerHTML += '<br/>';
			var phone = this.xmlFields.getAttribute('phone');
		    if(phone != ''){
				contentDiv.innerHTML += '<?php echo trim(t('phone'));?> : ' + phone;
				contentDiv.innerHTML += '<br/>';
			};
		    contentDiv.innerHTML += this.xmlFields.getAttribute('email');
		    contentDiv.innerHTML += '<br/>';
			
		    tProducts = [];
		    if( this.xmlFields.getAttribute('field_wheels_value') == 1 ){ tProducts.push( '<?php echo trim(t('Wheels'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_rims_value') == 1 ){ tProducts.push( '<?php echo trim(t('Rims'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_tyres_value') == 1 ){ tProducts.push( '<?php echo trim(t('Tyres'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_computers_value') == 1 ){ tProducts.push( '<?php echo trim(t('Computers'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_pedals_value') == 1 ){ tProducts.push( '<?php echo trim(t('Pedals'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_helmets_value') == 1 ){ tProducts.push( '<?php echo trim(t('Helmets'));?>' ) ; }
			if( this.xmlFields.getAttribute('field_footwear_value') == 1 ){ tProducts.push( '<?php echo trim(t('Footwear'));?>' ) ; } 
		    if( this.xmlFields.getAttribute('field_apparel_value') == 1 ){ tProducts.push( '<?php echo trim(t('Apparel'));?>' ) ; } 
		    if( this.xmlFields.getAttribute('field_accessories_value') == 1 ){ tProducts.push( '<?php echo trim(t('Accessories'));?>' ) ; } 
		    
		    if(tProducts.length > 0 )
		    {
		    	contentDiv.innerHTML += '<br/>';
			    contentDiv.innerHTML += '<b><?php echo trim(t('Products'));?> : </b>';
			    contentDiv.innerHTML += tProducts.join(' - ');
		    }    
	
			tServices = [];
			if( this.xmlFields.getAttribute('field_premium_value') == 'mavic_yellow' ){ tServices.push( '<?php echo trim(t('PREMIUM+'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_mp3_value') == 1 ){ tServices.push( '<?php echo trim(t('MP3'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_mavic_lab_value') == 1 ){ tServices.push( '<?php echo trim(t('MAVIC LAB'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_tech_dealer_value') == 1 ){ tServices.push( '<?php echo trim(t('TECH DEALER'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_filtre_un_value') == 1 ){ tServices.push( '<?php echo trim(t('TEST CENTER'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_filtre_deux_value') == 1 ){ tServices.push( '<?php echo trim(t('FILTRE 2'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_filtre_trois_value') == 1 ){ tServices.push( '<?php echo trim(t('FILTRE 3'));?>' ) ; }
		    if( this.xmlFields.getAttribute('field_filtre_quatre_value') == 1 ){ tServices.push( '<?php echo trim(t('FILTRE 4'));?>' ) ; }

		    
		    if(tServices.length > 0 )
		    {
		    	contentDiv.innerHTML += '<br/>';
			    contentDiv.innerHTML += '<b><?php echo trim(t('Services'));?> : </b>';
			    contentDiv.innerHTML += tServices.join(' - ');
		    }    
		    contentDiv.innerHTML += '<br/>';

		    contentDiv.innerHTML += '<b><a class="getdirection" target="_blank" href="http://maps.google.com/maps?daddr='+this.xmlFields.getAttribute('street')+' '+this.xmlFields.getAttribute('postal_code')+' '+this.xmlFields.getAttribute('city')+' '+this.xmlFields.getAttribute('countryname')+'"><?php echo trim(t('Get Direction'));?></a></b>';
		    
		    var queueImg = document.createElement("img");
		    queueImg.src = '/sites/default/themes/mavic/images/triangle_white_down.gif';
		    queueImg.className = 'queueImg';
		    
		    var closeImg = document.createElement("div");
		    closeImg.className = 'closeImg';
		    function removeInfoBox(ib) {
		      return function() {
		        ib.hide();
		      };
		    }
		    google.maps.event.addDomListener(closeImg, 'click', removeInfoBox(this));
	
		    div.appendChild(closeImg);
		    div.appendChild(titleDiv);
		    div.appendChild(contentDiv);
		    div.appendChild(queueImg);
	    
	    	div.style.display = 'none';
	    	
	    
	    Cufon.replace('h2.helvetica', {hover: true, ignore: { ul: true }, "font-family": "Helvetica75"});
	   }
	};
	
	
	/* Pan the map to fit the InfoBox.
	 */
	InfoBox.prototype.panMap = function() {

	  // if we go beyond map, pan map
	  var map = this.map_;
	  var bounds = map.getBounds();
	  if (!bounds) return;

	  // The position of the infowindow
	  var position = this.latlng_;

	  // The dimension of the infowindow
	  var iwWidth = this.width_;
	  var iwHeight = this.height_;

	  // The offset position of the infowindow
	  var iwOffsetX = this.offsetHorizontal_;
	  var iwOffsetY = this.offsetVertical_;

	  // Padding on the infowindow
	  var padX = 40;
	  var padY = 40;

	  // The degrees per pixel
	  var mapDiv = map.getDiv();
	  var mapWidth = mapDiv.offsetWidth;
	  var mapHeight = mapDiv.offsetHeight;
	  var boundsSpan = bounds.toSpan();
	  var longSpan = boundsSpan.lng();
	  var latSpan = boundsSpan.lat();
	  var degPixelX = longSpan / mapWidth;
	  var degPixelY = latSpan / mapHeight;

	  // The bounds of the map
	  var mapWestLng = bounds.getSouthWest().lng();
	  var mapEastLng = bounds.getNorthEast().lng();
	  var mapNorthLat = bounds.getNorthEast().lat();
	  var mapSouthLat = bounds.getSouthWest().lat();

	  // The bounds of the infowindow
	  var iwWestLng = position.lng() + (iwOffsetX - padX) * degPixelX;
	  var iwEastLng = position.lng() + (iwOffsetX + iwWidth + padX) * degPixelX;
	  var iwNorthLat = position.lat() - (iwOffsetY - padY) * degPixelY;
	  var iwSouthLat = position.lat() - (iwOffsetY + iwHeight + padY) * degPixelY;

	  // calculate center shift
	  var shiftLng =
	      (iwWestLng < mapWestLng ? mapWestLng - iwWestLng : 0) +
	      (iwEastLng > mapEastLng ? mapEastLng - iwEastLng : 0);
	  var shiftLat =
	      (iwNorthLat > mapNorthLat ? mapNorthLat - iwNorthLat : 0) +
	      (iwSouthLat < mapSouthLat ? mapSouthLat - iwSouthLat : 0);

	  // The center of the map
	  var center = map.getCenter();

	  // The new map center
	  var centerX = center.lng() - shiftLng;
	  var centerY = center.lat() - shiftLat;

	  // center the map to the new shifted center
	  map.setCenter(new google.maps.LatLng(centerY, centerX));

	};

	//utility function hide all infobxes
	function hideInfoBoxes()
	{
		if (markersArray) {
	    for (i in markersArray) {
		      if(markersArray[i].infobox)markersArray[i].infobox.hide();
		    }
		  }
	}	

	/*************************************************************
	custom textmarker
	**************************************************************/
	/* 
	 */
	function TextMarker(opts) {
	  google.maps.OverlayView.call(this);
	  this.latlng_ = opts.latlng;
	  this.map_ = opts.map;
	  this.xmlFields = opts.xmlFields;
	  this.offsetVertical_ = -42;
	  this.offsetHorizontal_ = -14;
	  this.height_ = 17;
	  this.width_ = 19;
	  this.display = true;

	  var me = this;
	  

	  // Once the properties of this OverlayView are initialized, set its map so
	  // that we can display it.  This will trigger calls to panes_changed and
	  // draw.
	  this.setMap(this.map_);
	}
	
	/* InfoBox extends GOverlay class from the Google Maps API
	 */
	 TextMarker.prototype = new google.maps.OverlayView();

	/* remove the DIV representing this TextMarker
	 */
	 TextMarker.prototype.remove = function() {
	  if (this.div_) {
	    this.div_.parentNode.removeChild(this.div_);
	  }
	};
	
	/* Redraw the Bar based on the current projection and zoom level
	 */
	 TextMarker.prototype.draw = function() {
	  // Creates the element if it doesn't exist already.
	  this.createElement();
	  if (!this.div_ ) return;
	  // Calculate the DIV coordinates of two opposite corners of our bounds to
	  // get the size and position of our Bar
	  var pixPosition = this.getProjection().fromLatLngToDivPixel(this.latlng_);
	  if (!pixPosition) return;

	  // Now position our DIV based on the DIV coordinates of our bounds
	  this.div_.style.width = this.width_ + "px";
	  this.div_.style.left = (pixPosition.x + this.offsetHorizontal_) + "px";
	  this.div_.style.height = this.height_ + "px";
	  this.div_.style.top = (pixPosition.y + this.offsetVertical_) + "px";
	  this.div_.style.display = 'block';
	  
	};
	
	/* Creates the DIV representing this textmarker in the floatPane.  If the panes
	 * object, retrieved by calling getPanes, is null, remove the element from the
	 * DOM.  If the div exists, but its parent is not the floatPane, move the div
	 * to the new pane.
	 * Called from within draw.  Alternatively, this can be called specifically on
	 * a panes_changed event.
	 */
	 TextMarker.prototype.createElement = function() {
	  var panes = this.getPanes();
	  var div = this.div_ ;
	
	  if (!div) {
	    // This does not handle changing panes.  You can set the map to be null and
	    // then reset the map to move the div.
	    div = this.div_  = document.createElement("div");
	    div.className="textmarker";
	    
	    div.innerHTML= '('+this.xmlFields.getAttribute('number')+')';
	    
	    panes.floatPane.appendChild(div);
	    
	  } else if (div.parentNode != panes.floatPane) {
	    // The panes have changed.  Move the div.
	    div.parentNode.removeChild(div);
	    panes.floatPane.appendChild(div);
	  } else {
	    // The panes have not changed, so no need to create or move the div.
	  }
	}
	/*****************************************************
	UTILS function for ajax retrieval remove it if use of jquery
	************************************************************/
	/**
	* Returns an XMLHttp instance to use for asynchronous
	* downloading. This method will never throw an exception, but will
	* return NULL if the browser does not support XmlHttp for any reason.
	* @return {XMLHttpRequest|Null}
	*/
	function createXmlHttpRequest() {
	 try {
	   if (typeof ActiveXObject != 'undefined') {
	     return new ActiveXObject('Microsoft.XMLHTTP');
	   } else if (window["XMLHttpRequest"]) {
	     return new XMLHttpRequest();
	   }
	 } catch (e) {
	   changeStatus(e);
	 }
	 return null;
	};

	/**
	* This functions wraps XMLHttpRequest open/send function.
	* It lets you specify a URL and will call the callback if
	* it gets a status code of 200.
	* @param {String} url The URL to retrieve
	* @param {Function} callback The function to call once retrieved.
	*/
	function downloadUrl(url, callback) {
	 var status = -1;
	 var request = createXmlHttpRequest();
	 if (!request) {
	   return false;
	 }

	 request.onreadystatechange = function() {
	   if (request.readyState == 4) {
	     try {
	       status = request.status;
	     } catch (e) {
	       // Usually indicates request timed out in FF.
	     }
	     if (status == 200) {
	       callback(request.responseXML, request.status);
	       request.onreadystatechange = function() {};
	     }
	   }
	 }
	 request.open('GET', url, true);
	 try {
	   request.send(null);
	 } catch (e) {
	   changeStatus(e);
	 }
	};

	/**
	 * Parses the given XML string and returns the parsed document in a
	 * DOM data structure. This function will return an empty DOM node if
	 * XML parsing is not supported in this browser.
	 * @param {string} str XML string.
	 * @return {Element|Document} DOM.
	 */
	function xmlParse(str) {
	  if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') {
	    var doc = new ActiveXObject('Microsoft.XMLDOM');
	    doc.loadXML(str);
	    return doc;
	  }

	  if (typeof DOMParser != 'undefined') {
	    return (new DOMParser()).parseFromString(str, 'text/xml');
	  }

	  return createElement('div', null);
	}

	/**
	 * Appends a JavaScript file to the page.
	 * @param {string} url
	 */
	function downloadScript(url) {
	  var script = document.createElement('script');
	  script.src = url;
	  document.body.appendChild(script);
	}

	/********************************************
	Specific function for mavic form
	*********************************************/


	function toggleparam(name)
	{
		if( $('#bullet_'+name).hasClass('checked') )
		{
			$('#bullet_'+name).removeClass('checked');
		}
		else
		{
			$('#bullet_'+name).addClass('checked');
			omniture_click(document.getElementById('bullet_'+name), 'shopinshop option_'+name);
		}
		searchLocations();
	}

	$(document).ready(function() {
	//add onclick functions for titles
		$('#options .title').click( function(){toggleparam( $(this).attr('id').replace('option_','') );} );
	});

	/******************
	Scrolling functions
	*******************/
	var scroll_length = 223;
	
	function scrollleft()
	{
		$('#shopresults').stop(true,true);
		if( parseInt( $('#shopresults').css('margin-left')) + scroll_length*3 >= 0 )
			document.getElementById('scroller_button_left').style.visibility = 'hidden';
		if( parseInt( $('#shopresults').css('margin-left')) < 0 )
			$('#shopresults').animate({marginLeft:'+='+(scroll_length*3)},500);
		document.getElementById('scroller_button_right').style.visibility = 'visible';
	}

	function scrollright()
	{
		$('#shopresults').stop(true,true);
		if( parseInt( $('#shopresults').css('margin-left')) - scroll_length*3 <= ( $('.shopitem').length-3)*-scroll_length )
			document.getElementById('scroller_button_right').style.visibility = 'hidden';
		if( parseInt( $('#shopresults').css('margin-left')) > ( $('.shopitem').length-3)*-scroll_length )
			$('#shopresults').animate({marginLeft:'-='+(scroll_length*3)},500);
		document.getElementById('scroller_button_left').style.visibility = 'visible';
	}

	/**********************
	Gmap autocomplete	
	***********************/
	$(document).ready(function() {
		$('#scroller_button_left').click(function(){scrollleft();});
		$('#scroller_button_right').click(function(){scrollright();});		  
	});

	function show_hide_countries()
	{
		$('#list_select_countries').css('top',$('#select_country').offset().top+22);
		$('#list_select_countries').css('left',$('#select_country').offset().left);
		
		show_hide_select('#list_select_countries');
		
	}	
</script>
		
<div  id="shopfinder">
	<img src="<?php print base_path().$field_shopinshop_img[0]['filepath'];?>" />
	<div id="shopinshop_desc">
		<p class="sisleft"><b><?php print $field_shopinshop_ingro[0]['value'].' ';?></b><?php print $field_shopinshop_intro[0]['value'].' ';?></p>
		<p class="sisright "><?php print $field_shopinshop_intro2[0]['value'].' ';?></p>
	</div>
	<div class="clear"></div>
	<div id="basicForm" style="position:relative;"> 
		<form onsubmit="searchLocations(); return false;">
			<input type="hidden" id="countrySelect" value="" /> 
			<label class="helvetica"><?php echo t('country : ');?></label>
		    <div id="select_country">
		    	<div id="selected_country" onclick="show_hide_countries()"><?php echo t('choose');?></div>		    	
			</div>
		</form>
	</div>
	<div class="clear" style="height:10px;"></div>
		<div id="options" class="shopinshop">
			<div id="optionleft" >
				<div class="optionblock">
					<div class="title" id="option_bike_sytem"  >
						<div class="bullet checkbox unchecked" id="bullet_bike_sytem">&nbsp;</div>
						<div class="label"><?php echo t('bike systems');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
						//$text = ;
						echo t('(wheels, rims, tyres, pedals, computers)');		
						?>
					</div>
					
				</div>				
			</div>
			<div id="optionright" style="">
				<div class="optionblock" >
					<div class="title" id="option_riders_equip">
						<div class="bullet checkbox unchecked" id="bullet_riders_equip">&nbsp;</div>
						<div class="label"><?php echo t('rider\'s equipment');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
							echo t('(helmets, footwear, apparel)');
						?>
					</div>
					
				</div>
			</div>
			<div class="clear"></div>
		</div>

	<div id="map" ></div>
	
	<div id="legend">
		<img src="/sites/default/themes/mavic/images/premiumplusshop.gif" style="" alt="<?php echo t('Premium+ shops');?>"/>
		<?php echo t('Premium+ shops');?>
	</div>

	<div id="sf_caroussel">
		<div id="scroller_button_left" style="visibility:hidden" >
			<img src="/sites/default/themes/mavic/images/scroller_left.jpg" alt="scroll left"/>
		</div>
		<div id="sf_wrapper" >
			<div class="shopresults" id="shopresults" >
				
			</div>
		</div>
		<div id="scroller_button_right" style="visibility:hidden" >
			<img style="z-index: 10000" src="/sites/default/themes/mavic/images/scroller_right.jpg" alt="scroll right"/>
		</div>
	</div>
	
	
	<?php print $countryList; ?>
</div>