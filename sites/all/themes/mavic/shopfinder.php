<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var map;
    var geocoder;
    var markersArray = [];
    var circle = null;
    var currentDatas;
    var fInfoBox;
    var panes = null;
	var onlyCountry = false;
    
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
			  latLong = {"error":"ajax failed"}; 
			}
		}) 
		return latLong; 
	}

    function sf_initialize() {
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

        $('#body').click( function(){ $('#list_select_radius').css('display','none'); });
        
        <?php 
		$contriesName = sfGetCountries();
		if(isset($_GET['shopcountry']) && (isset($contriesName[$_GET['shopcountry']]) && !empty($contriesName[$_GET['shopcountry']]))) :
		?>
		var latLong = {"error":false,"city":"","country":"<?php print $contriesName[$_GET['shopcountry']]; ?>","country_code":"<?php print $_GET['shopcountry']; ?>"};
		
			<?php else : ?> 
		var latLong = getVisitorLocation();
			<?php endif; ?>
		if(!latLong.error && ((latLong.latidude != 0 && latLong.longitude != 0) || (latLong.country_code != "" && latLong.country != "")) ) { // if visitor location is found, display nearest shop
			if(latLong.city != "" && latLong.country != "") {
				$(document).ready(function() {
					document.getElementById('addressInput').value = latLong.city + " , " + latLong.country;
				});
				searchLocations(latLong.city + " , " + latLong.country);
			} else if(latLong.country_code != "" && latLong.country != "") {
				$(document).ready(function() {
					document.getElementById('addressInput').value = latLong.country;
				});
				searchLocations(latLong.country, latLong.country_code);
			} else {
				searchLocationsNear(new google.maps.LatLng(latLong.latidude,latLong.longitude));
			}
		}
    }
	
	function searchLocations(cityCountry, countryCode) {
		var address = document.getElementById('addressInput').value;
		if(cityCountry != undefined) address = cityCountry; // addressInput field can be not initialized yet
		var geodata = {'address': address};
		if(countryCode != undefined) {
			geodata.region = countryCode; 
			//onlyCountry = true;
		}
		geocoder.geocode( geodata, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				if (results[0].types[0] == "country") {
					searchLocationsNear(results[0].geometry.location, results[0].geometry.viewport, results[0].address_components[0].short_name);
					//onlyCountry = false;
				} else {
					searchLocationsNear(results[0].geometry.location);
				}
				
			} else {
					document.getElementById('shopresults').innerHTML = '<div  class="noresult"><?php echo t('No results found.'); ?></div>';
					document.getElementById('scroller_button_left').style.visibility = 'hidden';
					document.getElementById('scroller_button_right').style.visibility = 'hidden';
			}
		});
	}

	function searchLocationsNear(center, bounds, country){
		if (bounds != undefined && country != undefined) {
			document.getElementById('selected_radius').innerHTML = '---';
			var searchUrl = '/storefinder/find/2/0/0/' + country;
		} else {
			var radius = document.getElementById('radiusSelect').value;
			if (document.getElementById('selected_radius').innerHTML == "---") {
				var elem = document.getElementById("list_select_radius_default");
				if (typeof elem.onclick == "function") {
					elem.onclick.apply(elem);
				}
			}
			var searchUrl = '/storefinder/find/0/' + center.lat() + '/' + center.lng() + '/' + radius;
			
		}
		
		fInfoBox.hide();
		
		if($('#bullet-mavic-lab').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-mp3').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-tech-dealer').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-premium').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-test-center').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-filtre2').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-filtre3').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		if($('#bullet-filtre4').hasClass('checked'))searchUrl += '/1'
		else searchUrl  += '/0';
		
		
		downloadUrl(searchUrl, function(data) {

			currentDatas = data;
			//recenter map
			map.setCenter(center);
			if (bounds != undefined && country != undefined) {
				if(circle!= null)circle.setMap(null);
				map.fitBounds(bounds);
				map.setZoom(map.getZoom());
			} else {
				z=12;
				if( radius > 5 ){z=11};
				if( radius > 10 ){z=10};
				if( radius > 25 ){z=9};
				if( radius > 50 ){z=8};
				if( radius > 100 ){z=7};
				//if( radius > 200 ){z=6};
				map.setZoom(z);//drawClusterMarkers(); marker draw is called during zoom thrue event listener

				//remove old circle
				if(circle!= null)circle.setMap(null);
				// show the circle
				circle = new MapCircleOverlay(center,radius, 0.8, "#000000",0.8, "#000000", 0.35 );

				// And we attach it to the map
				circle.setMap( map );

			}
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
				html += '<?php echo t('distance')?> : ';
				var lang = '<?php print $lang ?>';
				var distance = xmlMarkers[i].getAttribute('distance');
				var miles = distance * 1.609344;
				if (lang == 'en') {html += Math.round(miles * 100)/100;html += 'MI <br/>';} else {html += Math.round(distance * 100)/100;html += 'KM <br/>';};
				
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
			contentDiv.innerHTML += '<b><?php echo trim(t('Distance'));?> : </b>';
			var distance = this.xmlFields.getAttribute('distance');
			var miles = distance * 1.609344;
			var lang = '<?php print $lang ?>';
			if (lang == 'en') {contentDiv.innerHTML += Math.round(miles * 100)/100; contentDiv.innerHTML += 'MI <br/>';} else {contentDiv.innerHTML += Math.round(distance * 100)/100; contentDiv.innerHTML += 'KM <br/>';}

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

	/***************************************************
	Circle polygon
	****************************************************/
	// This file adds a new circle overlay to Google Maps v3
	// Original Google Maps API v2 File : http://dawsdesign.com/drupal/google_maps_circle_overlay
	// Ported to GMaps v3 by http://florent.clairambault.fr/
	 
	/**
	 * Filled circle overlay
	 */
	var MapCircleOverlay = function(center, radius, strokeWeight, strokeColor, strokeOpacity, fillColor, fillOpacity) {
		
		this.center = center;
		this.radius = radius;
		this.strokeWeight = strokeWeight;
		this.strokeColor = strokeColor;
		this.strokeOpacity = strokeOpacity;
		this.fillColor = fillColor;
		this.fillOpacity = fillOpacity;
	 
		this.circlePolygon = null;
	 
		// 50 lines look like a pretty good circle
		this.numPoints = 50;
	 
		this.d2r = Math.PI / 180;
	 
		this.bound = null;
	 
		this.setCenter = function( latLng ) {
			this.center = latLng;
			this.draw();
		};
	 
		this.setRadius = function( radius ) {
			this.radius = radius;
			this.draw();
		};
	};
	 
	/* base class overloads follow this comment */
	MapCircleOverlay.prototype = new google.maps.OverlayView;
	 
	// Calculate all the points and draw them
	// Base method must be implemented like this
	MapCircleOverlay.prototype.draw = function() {	
		if ( ! isFinite( this.radius ) || ! isFinite( this.center.lat() ) || ! isFinite( this.center.lng() ) ) {
			if ( console != undefined ) 
				console.error('Radius has to be a number !');
			return;
		}
	 
		circleLatLngs = new Array();
	 
	        // Remove the "* 0.621371192" to use miles instead of kilometers
		var circleLat = this.radius * 0.621371192 * 0.014483;  // Convert statute into miles and miles into degrees latitude
		var circleLng = circleLat / Math.cos( this.center.lat() * this.d2r);
	 
		// 2PI = 360 degrees, +1 so that the end points meet
		for (var i = 0; i < this.numPoints+1; i++) { 
			var theta = Math.PI * (i / (this.numPoints / 2)); 
			var vertexLat =  this.center.lat() + (circleLat * Math.sin(theta)); 
			var vertexLng =  this.center.lng() + (circleLng * Math.cos(theta));
			var vertextLatLng = new google.maps.LatLng(vertexLat, vertexLng);
		
			circleLatLngs.push( vertextLatLng );
		}
	 
		// Before drawing the new polygon, we have to remove the old one
		this.clear();
	 
		this.circlePolygon = new google.maps.Polygon({
		  paths: circleLatLngs,
		  strokeColor: this.strokeColor,
		  strokeOpacity: this.strokeOpacity,
		  strokeWeight: this.strokeWeight,
		  fillColor: this.fillColor,
		  fillOpacity: this.fillOpacity,
		  clickable: false
		});
	 
		this.circlePolygon.setMap( this.map );
	};
	 
	MapCircleOverlay.prototype.clear = function() {
		if ( this.circlePolygon != null ) {
			this.circlePolygon.setMap( null );
			this.circlePolygon = null;
		}
	};
	 
	MapCircleOverlay.prototype.onRemove = function() {
		this.clear();
	};
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
	function show_hide_options()
	{  
		$('#options').slideFadeToggle(500);
	}
	jQuery.fn.slideFadeToggle = function(speed, easing, callback) {
	  return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);  
	};


	function toggleparam(name)
	{
		if( $('#bullet-'+name).hasClass('checked') )
		{
			$('#bullet-'+name).removeClass('checked');
		}
		else
		{
			$('#bullet-'+name).addClass('checked');
			omniture_click(document.getElementById('bullet-'+name), 'shop finder option_'+name);
		}
		searchLocations();
	}

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
		//add onclick functions for titles
		$('#options .title').click( function(){toggleparam( $(this).attr('id').replace('option-','') );} );
		//prefiltering param
		<?php if(isset($_GET['shopfilter'])) : ?>
		toggleparam('<?php print $_GET['shopfilter'];?>');
		show_hide_options();
		<?php endif ?>

		$('#scroller_button_left').click(function(){scrollleft();});
		$('#scroller_button_right').click(function(){scrollright();});

		$(function() {
		    $("#addressInput").autocomplete({
		      //This bit uses the geocoder to fetch address values
		      source: function(request, response) {
		        geocoder.geocode( {'address': request.term }, function(results, status) {
		          response($.map(results, function(item) {
		            return {
		              label:  item.formatted_address,
		              value: item.formatted_address
		            }
		          }));
		        })
		      }
		    }
		    );
		});
		  
	});

	function show_hide_radius()
	{
		$('#list_select_radius').css('top',$('#select_radius').offset().top+22);
		$('#list_select_radius').css('left',$('#select_radius').offset().left);
		
		show_hide_select('#list_select_radius');
		
	}
	
	var default_text_shop_field = "<?php echo trim(t('Enter a city/country or postal code')); ?>";
	function empty_shop_field(field) {
		$('#addressInput').css("color","#000000");
		if(field.value == default_text_shop_field) {
			field.value = "";
		}
	}
	
	function default_shop_field(field) {
		if(field.value == "") {
			field.value = default_text_shop_field;
			$('#addressInput').css("color","#7D7D7D");
		}
	}
	
</script>
		
<div  id="shopfinder">
	<div id="basicForm" style="position:relative;"> 
		<form onsubmit="searchLocations(); return false;">
			<input type="text" id="addressInput" value="<?php echo t('Enter a city/country or postal code'); ?>" onfocus="empty_shop_field(this)" onblur="default_shop_field(this)" /> 
		    <div id="select_radius">
			 <?php if ($lang == "en") { ?>
		    	<div id="selected_radius" onclick="show_hide_radius()">25 mi</div>		    	
			</div>
			<input type="hidden" value="40" id="radiusSelect"/>
			<?php } else { ?>
		    	<div id="selected_radius" onclick="show_hide_radius()">25 km</div>		    	
			</div>
			<input type="hidden" value="25" id="radiusSelect"/>
			<?php } ?>
		    <a href="#" onclick="searchLocations()" id="searchButton"><?php echo t('ok');?></a>
		</form>
	</div>
	<div class="clear" style="height:10px;"></div>
    <div id="moreoptions" >
			<a class="button_view" onclick="show_hide_options(); return false;"><?php echo t('more options');?></a>
		</div>
		<div id="options" >
			<div id="optionleft" >
				<?php if (FALSE) : ?>
				<div class="optionblock">
					<div class="title" id="option-mavic-lab"  >
						<div class="bullet checkbox unchecked" id="bullet-mavic-lab">&nbsp;</div>
						<div class="label"><?php echo t('Mavic Lab');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
						//$text = ;
						echo t('Mavic Lab is a website to customize Mavic wheels. It enables you to create unique models, more ultimate than those available in the classic range.');		
						?>
					</div>
				</div>
				<?php endif ?>
				
				<div class="optionblock">
					<div class="title" id="option-mp3">
						<div class="bullet checkbox unchecked" id="bullet-mp3">&nbsp;</div>
						<div class="label"><?php echo t('MP3');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
						$text = str_replace('www.mavicmp3.com', '<a class="mp3" href="http://www.mavicmp3.com" target="_blank">www.mavicmp3.com</a>', t('MP3 warranty dealer. More information on www.mavicmp3.com'));
						echo $text;
						?>
					</div>
					
				</div>
				<div class="optionblock">
					<div class="title" id="option-premium">
						<div class="bullet checkbox unchecked" id="bullet-premium">&nbsp;</div>
						<div class="label"><?php echo t('Premium+ shops');?> <img src="/sites/default/themes/mavic/images/premiumplusshop.gif" style="" alt="<?php echo t('Premium+ shops');?>"/></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
							$text = 'Stores selling the entire Mavic range';
							echo t($text);
						?>
					</div>
					
				</div>
			</div>
			<div id="optionright" style="">
				<div class="optionblock" >
					<div class="title" id="option-tech-dealer">
						<div class="bullet checkbox unchecked" id="bullet-tech-dealer">&nbsp;</div>
						<div class="label"><?php echo t('Tech dealer');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
							$text = 'Mavic’s dealers technically trained on the current range';
							echo t($text);
						?>
					</div>
					
				</div>
				<?php if(isset($_GET['shopfilter']) && $_GET['shopfilter'] == "test-center"){ ?>
				<div class="optionblock">
					<div class="title" id="option-test-center">
						<div class="bullet checkbox unchecked" id="bullet-test-center">&nbsp;</div>
						<div class="label"><?php echo t('Test Center');?></div>
					</div>
					<div class="clear"></div>
					<div class="text">
						<?php 
							$text = 'Stores where you can test stuff';
							echo t($text);
						?>
					</div>
					
				</div><?php } ?>

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
	
	
  	<ul id="list_select_radius">
		<?php if ($lang == "en") { ?>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=8;$('#selected_radius').html('5 mi')">5 mi</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=16;$('#selected_radius').html('10 mi')">10 mi</a></li>
    	<li><a id="list_select_radius_default" href="#" onclick="$('#radiusSelect').get(0).value=40;$('#selected_radius').html('25 mi')">25 mi</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=80;$('#selected_radius').html('50 ')">50 mi</a></li>
   	 	<li><a href="#" onclick="$('#radiusSelect').get(0).value=160;$('#selected_radius').html('100 mi')">100 mi</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=320;$('#selected_radius').html('200 mi')">200 mi</a></li>	
		<?php } else { ?>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=5;$('#selected_radius').html('5 km')">5 km</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=10;$('#selected_radius').html('10 km')">10 km</a></li>
    	<li><a id="list_select_radius_default" href="#" onclick="$('#radiusSelect').get(0).value=25;$('#selected_radius').html('25 km')">25 km</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=50;$('#selected_radius').html('50 km')">50 km</a></li>
   	 	<li><a href="#" onclick="$('#radiusSelect').get(0).value=100;$('#selected_radius').html('100 km')">100 km</a></li>
    	<li><a href="#" onclick="$('#radiusSelect').get(0).value=200;$('#selected_radius').html('200 km')">200 km</a></li>
		<?php } ?>
    </ul>
</div>