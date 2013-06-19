if(language != 'ja') {
	Cufon.replace(".helvetica", {
		hover: true, 
		ignore: {
			ul: true
		}, 
		"font-family": "Helvetica75"
	});//Helvetica65-Medium
}

$(window).bind('resize', function () {
	$("#body-background").ezBgResize();
	checkSize();
	var windowHeight = 0;
	if (typeof(window.innerHeight) == 'number') {
		windowHeight = window.innerHeight;
	}
	else {
		if (document.documentElement && document.documentElement.clientHeight) {
			windowHeight = document.documentElement.clientHeight;
		}
		else {
			if (document.body && document.body.clientHeight) {
				windowHeight = document.body.clientHeight;
			}
		}
	}
	if($('body').hasClass('cxr_landing_page') || $('body').hasClass('crossmax_landing_page') ){			
		if (windowHeight < 814){
			$('#topanchorlink').css({
				top:'auto',
				bottom:'59px'
			});
		} else {
			$('#topanchorlink').css({
				top:'733px',
				bottom:'auto'
			});
		}
	}
	if($('#mavic_search').length){
		var sh = windowHeight - 105;
		if($('#mavic_search').height() < sh){
			$('#mavic_search').css({
				'height':sh+'px'
				});
		}
	}
});	


$(document).ready(function() {
	//if(mobileUA == 'tablet' || mobileUA == 'smartphone' ){
	if (isTierIphone || isTierTablet) {
		$(document).bind('orientationchange', function (e){
			if(!onHomePage){
				checkSize();
			} else {
				homeCheckSize();
			}
			
		});
		if(!onHomePage){
			$('body').css({
				"width":"1270px",
				'background':'grey'
			});
			$('#subcontainer').css({
				'margin-top':'56px'
			});
			$('#logo_container').add("#top_menu").add("#language-select-list").add("#list_select_langue").css({
				"position":"absolute", 
				"z-index": "1000"
			});
			$("#footer").add("#top_menu").css({
				"min-width":"1270px"
			});
			
			$('#footer').css({
				"height" : $('#footer').height() + "px", 
				"margin-bottom":"0px"
			}); //force la hauteur du footer pour ipad
			$('#body-background').add('#top_menu_img').remove();
			//remove mouseOver listener on range page as it overwritte mouseClick 
			if ($('#content_gamme_items').length > 0){
				$('#content_gamme_items .product_item').add('#content_gamme_items .product_image').unbind('mouseout').unbind('mouseover').removeAttr('onmouseover').removeAttr('onmouseout');
			}
		} else {
			$("#menu .menu_main").css({
				"margin-top":"20px"
			});
			$("#home_right_content_container").css({
				"margin-top": "20px"
			});
			
			$(".top_menu_item.last").css({
				"width":"215px",
				"position":"static",
				"margin":"0px"
			});
			$('<div class="clear"></div>').insertAfter("#menu");
		}
		$("#language-select-list").add("#list_select_langue").css({
			"background-color": "#FFFFFF",
			"border": "1px solid #7D7D7D",
			"bottom": "28px",
			"display": "none",
			"margin": "-1px 0 0",
			"padding": "0",
			"position": "static",
			"right": "6px",
			"white-space": "nowrap",
			"width": "152px",
			"z-index": "1000"
		});
		$('#top_black_screen').add('#black_screen').remove();

		$('#submenu_MAVICItem').css('width','805px');

		$("#language-select-list").detach().appendTo("#footer .left").css('display','inline-block');

		$("#top_menu.front").css({
			"width": "993px",
			"position": "static"
		});
		if(onHomePage){
			$(window).trigger('resize');
		}
	} else {
		$('#subcontainer').css({
			'margin-top':'45px',
			'padding-bottom':'59px'
		});
	}
	checkSize();
	//curtail encarts content text if needed
	curtailtext('.link .text .content',40,3);
	curtailtext('.right_content_poster_txt',40,5);
	$('.right_content_text').removeClass('hover');

	$('input[type="checkbox"]').ezMark();
	$('input[type="radio"]').ezMark();
	
	//activing current nearest separators
	if( $('#menu a.current').length > 0 ){
		$('#menu a.current').next('.menu_separator').addClass('current');
		//can be a submenu, need another step
		if( $('#menu a.current').prev('.menu_separator').length > 0 ) $('#menu a.current').prev('.menu_separator').addClass('current');
		else $('#menu a.current').prev('.submenu').prev('.menu_separator').addClass('current');
	}
	//top menu click
	$('#top_menu a').click(function(e){
		e.preventDefault();
	});
	$('#top_menu div.top_menu_item:not(.last)').click(function(){
		window.location = $(this).children('a.link').attr('href');
	});
	//main menu and top menu hover
	$('#menu .menu_main > a').add('#top_menu div.top_menu_item:not(.last):not(.current)').mouseenter(function(){
		//removing other active links
		$(this).siblings('.active').removeClass('active');

		//hidding other submenu
		$('.submenu:not(#sub'+$(this).attr('id')+')').css('display','none');

		//activing the link
		$(this).addClass('active');

		//activing the nearest separators
		var previous_separator = $(this).prev('.menu_separator');
		//can be a submenu, need another step
		if( $(this).prev('.menu_separator').length > 0 ) $(this).prev('.menu_separator').addClass('active');
		else $(this).prev('.submenu').prev('.menu_separator').addClass('active');

		$(this).next('.menu_separator').addClass('active');

		//showing overlay
		if(!isTierIphone && !isTierTablet){
			if($('#black_screen').css('display') == 'none')
			{
				if(onHomePage){
					$('#black_screen').css('width', $('#container').width())
					.css('height', $('#container').height());
				}

				$('#black_screen')
				.css('opacity', '0.5')
				.fadeIn(200)
				.unbind('mouseover').mouseover(function(){
					getOutOfMenu();
				}); //emulate link "mouseleave"
				if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod'){
					$('#black_screen').css("top", "25px");
				}

				$('#top_black_screen').css('opacity', '0.5').fadeIn(200);
			}
			if($('#black_screen').css('display') == 'none')
			{
				if(onHomePage){
					$('#black_screen').css('width', $('#container').width())
					.css('height', $('#container').height());
				}

				$('#black_screen')
				.css('opacity', '0.5')
				.fadeIn(200)
				.unbind('mouseover').mouseover(function(){
					getOutOfMenu();
				}); //emulate link "mouseleave"
				if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod'){
					$('#black_screen').css("top", "25px");
				}

				$('#top_black_screen').css('opacity', '0.5').fadeIn(200);
			}

		}
		//showing submenu
		if ($(this).attr('id') == "menu_MAVICItem") 
		{
			if ($(this).hasClass('disc')) 
			{
				$('#sub'+$(this).attr('id')).css('display','block').css('top', ($(this).position().top));
			} 
			else 
			{
				$('#sub'+$(this).attr('id')).css('display','block').css('top', ($(this).position().top)-142);
				if (language != 'ja'){
					$('div.complement p.description').ellipsis();
				} 
				else 
				{
					$('div.complement p.description').css({
						'height':'100%'
					});
					curtailtext('div.complement p.description',45,5);
				}
			}
		}
		else 
		{
			$('#sub'+$(this).attr('id')).css('display','block').css('top', $(this).position().top);
		}
		//hidding select for ie
		if ($.browser.msie && $.browser.version.substr(0,1)<7)
		{
			$('select').css('visibility','hidden');
		}
	});
	$('#menu .menu_separator').mouseenter(function(){
		removeActiveMenu();
	});
	if (language != 'ja'){
		$('div.complement p.description').ellipsis();
	} else {
		$('div.complement p.description').css({
			'height':'100%'
		});
		curtailtext('div.complement p.description',45,5);
	}
	//mousemove behavior
	$("#altpopdisplayer").appendTo('body');
	$('#content_gamme_items').mousemove(function(e){
		if((e.currentTarget.id!=e.target.id)&&(e.target.id!='associated_products')){
			$("#altpopdisplayer").show();

			posX = e.pageX +15;
			posY = e.pageY -$("#altpopdisplayer").height()-30;
			if(posX > 800){
				posX=posX-270;
			}

			$("#altpopdisplayer").css({
				top: (posY) + "px",
				left: (posX) + "px"
			});
		}
	});
	//same behavior 
	$('#filters_content').mousemove(function(e){

		$("#altpopdisplayer").show();

		posX = e.pageX +15;
		posY = e.pageY ;
		if(posX > 800){
			posX=posX-270;
		}

		$("#altpopdisplayer").css({
			top: (posY) + "px",
			left: (posX) + "px"
		});
	});
			
			
	//same behavior 
	$('.feature-image').mousemove(function(e){
		$("#altpopdisplayer").show();
		
		posX = e.pageX +15;
		posY = e.pageY ;
		if(posX > 800){
			posX=posX-270;
		}

	
		$("#altpopdisplayer").css({
			top: (posY) + "px",
			left: (posX) + "px"
		});
	});
		
			
			
	
	//mouse move in product page for associated
	$("#associated_products").mouseout(function(e){
		$("#altpopdisplayer").hide();
	});
	
	//mouse move in product page for technologies
	$(".feature-image").mouseout(function(e){
		$("#altpopdisplayer").hide();
	});

	//search behavior
	if( $('#search_input').length > 0 ){
		$('#search_hint').click(function(){
			$('#search_form .submit').show(); //show ok button
			$(this).hide().next().show().focus(); //hide the hint and show the input and give it focus
		});

		//input leaving
		$('#search_input').blur(function(){
			if( $(this).val() == '' ){
				$('#search_form .submit').hide(); //hide ok button
				$(this).hide().prev().show(); //hide the input and show the hint
			}
		});
	}

	//download list background
	if( $('.download_content').length > 0 ){
		$('.download_content:odd').addClass('odd');
	}
	if( $('.archive_button').length > 0 ){
		$('.archive_button .button_view').click(function(e){
			e.preventDefault();
			$(this).parent().hide().next('.archive_files').fadeIn();
		});
	}
	var closeSelect =  function(){
		$('.selectMavicSkin,#list_select,#list_select_langue,#list_select_radius').css({
			display:'none'
		});
	}
	//fixe top google search autocompletion 
	$(document).bind('DOMNodeInserted', function(evt){
		if(evt.target.nodeName == "TABLE"){
			sgtop();
		}
	});
	var sgtop = function(){		
		if($('input').hasClass('scroll_autocomplete')){
			var thistop = $('.scroll_autocomplete').offset().top + 24;
			$('.gsc-completion-container').css({
				top:thistop
			});
		}
	}
	$('#search_input, #edit-keys').bind('focus blur', function(){
		$(this).toggleClass('scroll_autocomplete')
		});
	$('#body').click(closeSelect);
	$('#container').scroll(closeSelect).scroll(sgtop);
	//Family filters : check for allready checked filters
	if ($('#filters_content').length > 0) 
	{
		if (discFilterOff) {
			if (cfMtb) {
				removeMtbProd(mtbcfNid, roadcfNid, roadcfRNid); 
				checkForChecked();
				$("#filterField"+cfEl+"").closest('.filter').remove();
				if($("#filterField"+cfEl+"")){
					$("#filterField"+cfEl+"").closest('.filter').remove();
				}//in case of duplicates...
			} else {
				checkForChecked();
				if (filtPHide) {
					$("#filterField"+cfEl+"").closest('.filter').remove();
					if($("#filterField"+cfEl+"")){
						$("#filterField"+cfEl+"").closest('.filter').remove();
					}//in case of duplicates...
				}
			}
		} else {
			checkForChecked();
		}
			
	}
	//landing page
	if ($('#landing_diaporama').length > 0){
		landing_init('diapo','nav');
	}
	if ($('#ss2012_landing_diaporama').length > 0){
		landing_init('diapo','nav');
		landing_init('clima_ride_block','climaride_nav');
	}
	if ($('#video_tyre').length > 0){
		$('#main_content').css({
			'padding':'0 0 5px 0',
			'margin':0,
			width:'741px'
		});
	}
	if ($('#mp3_landing_page').length > 0){
		$('#main_content').css({
			'padding':'0 0 5px 0',
			'margin':0,
			width:'741px'
		});
	}
	//vimeo encart over
	if( $('.right_content_poster').length > 0 ){
		$('.right_content_poster').hover(function(){
			$(this).add('.right_content_text').toggleClass('hover');
		});
	}

});
function touchHandler(event)
{
	var touches = event.changedTouches,
	first = touches[0],
	type = "";

	switch(event.type)
	{
		case "touchstart" :
			type = "mousedown"; 
			break;
		case "touchmove":
			type="mousemove";
			$('#footer').hide();
			$('#top_menu').hide();
			break;        
		case "touchend":
			type="mouseup"; 
			break;
		default:
			return;
	}
	var simulatedEvent = document.createEvent("MouseEvent");
	simulatedEvent.initMouseEvent(type, true, true, window, 1,
		first.screenX, first.screenY,
		first.clientX, first.clientY, false,
		false, false, false, 0/*left*/, null);

	first.target.dispatchEvent(simulatedEvent);
	event.preventDefault();
}

function debugIpad() {
	$('#top_menu').html('<div class="top_menu_item">' + window.pageYOffset + 'px et wh : ' + $(window).height() + '</div>');
			 
}

function ipadFix() {
	$('#footer').show().css({
		"top":$(window).height() + window.pageYOffset - $('#footer').height() + "px"
		});
	$('#top_menu').show().css({
		"top":window.pageYOffset + "px"
		});
			
}
/*curtail target text (tar) according to max height (cl) (and number of symbol to be removed at a time for japanese (step))
*replace last word with elipsis
*/
function curtailtext(tar,cl,step)
{
	$(tar).each(function(){
		if (cl < $(this).height() ){
			var deja = false;
			var ett = $(this).text();
			var et = ett.split(" ");
			var ettl = ett.length;
			while(cl < $(this).height() ){
				if (language != 'ja'){
					step = et.pop().length;
				} 
				if (deja == true){
					step += 4;
				}
				ettl -= step;
				var nett = ett.substr(0, ettl) + "...";
				$(this).text(nett);
				deja = true;
			}
		}
	});
}	


//linking and scrolling for news in menu
$(function() {
	$(".scrollable .big").each(function(){
		$(this).css('cursor','pointer')
		.click(function() {
			window.location = $(this).parent().children('.link').attr('href');
		});
		if ($(this).parent().attr('id') == 'blockvideo')
		{
			$(this).hover(
				function () {
					$(this).css({
						'margin-top':'-133px'
					});
				},
				function () {
					$(this).css({
						'margin-top':'0'
					});
				}
				);
		};
		
	});
	$(".scrollable").scrollable(
		/*{
				onSeek: function(){
					$(".scrollable .big").each(function(){
						$(this).css('cursor','pointer')
						.click(function() {
							window.location = $(this).parent().children('.link').attr('href');
						});
					});
				}
		}*/
		);

});

var onHomePage=false;//this variable is set to true if we are on the home page


function checkSize()
{
	$('#tm_img').width($('#body-background img').width());//resize bg img of top menu
	//$('#container').css("height", ($(window).height()-$('#footer').height())+"px");
	if( ($(window).width() <  1270 || onHomePage) && (!isTierIphone && !isTierTablet) )
	{
		$('#right_content').detach().appendTo("#menu").addClass("small_size");
	}
	else
	{
		$('#right_content').detach().appendTo("#right_content_container").removeClass("small_size");
	}

	var marge=47;
	if ($.browser.msie && $.browser.version.substr(0,1)<=7){
		marge=50;
	}
	if(!isTierIphone && !isTierTablet){
		$('#black_screen').css({
			'width': $('body').width() + 'px',
			'height': $(window).height() + 'px'
		});
		
	} else {
		
		if(onHomePage){
			$("#footer.home_footer").css({
				"position": "absolute", 
				"bottom": "0", 
				"z-index": "1000"
			});

		} else {
			var h = $(document).height();
			$('#footer').css({
			'position':'static',
			'top':h + 'px'
		});
		}
	}
	positionLogo();
	checkContentHeight();
}

//force la hauteur du contenu en cas de page courte
function checkContentHeight(){
	return '';
//commented for i-pad fix
/*$('#main_content').height('auto');
	h_main = $('#main_content').height();
	h_min = $('#container').height() - parseInt($('#subcontainer').css('padding-bottom').replace(/px/,'')) - parseInt($('#subcontainer').css('margin-top').replace(/px/,''));
	$('#main_content').css('min-height', h_min);
	if (h_min > h_main) {
		$('#main_content').css('height', h_min); // for IE6
	}*/
//i-pad fix	

/*$('#main_content').height(function(index,value){
		if(isNaN(value)) return '';
		if(typeof shopfinderpage!='undefined' ) return '';//no check on shopfinder node page (node-shopfinder.tpl.php)
		var h = $('#container').height() - parseInt($('#subcontainer').css('padding-bottom').replace(/px/,'')) - parseInt($('#subcontainer').css('margin-top').replace(/px/,''));
		if( isNaN(h) )return '';
		if(h < 500 ) return '';
		if(value < h || $(this).hasClass('hforced')) {
			$('#main_content').addClass('hforced');
			return h;
		}
		return ''; //ne force rien, laisse la main a la feuille de style
	}); */
}
function positionLogo() //reposition the logo according to scrollbar for content
{
	if (isTierIphone || isTierTablet) {
		if(onHomePage){
		/*	$("#home_logo_container").css({
				"position": "static", 
				"margin-left": "15px", 
				"z-index": "3", 
				"margin-bottom": "59px"
			});
			*/
		} else {
			$('#logo_container').css({
				'position':'absolute',
				'top':'auto',
				'bottom':'60px',
				'left':'1102px',
				'right':'auto'
			});
		}
	} else {
		if($(window).width() <  1270) {
			$('#logo_container').css({
				'left': '15px', 
				'bottom': $('#footer').height() + 23 + 'px'
				});
			if($('#productCompareContainer').css('display')=='block'){//do not display logo over productcompare screen
				$('#player, #logo_container').css('display','none');
			}

		} else {
			$('#logo_container').css('left', ($('#container').get(0).clientWidth - 168)+'px');
			$('#logo_container').css('bottom', '59px');
			$('#logo_container').css('top', 'auto');
		}
	}
}




var curOverItem=0;
function overItem(index)
{
	if(typeof(itemtimer)!='undefined')clearTimeout(itemtimer[index]);
	outItem(curOverItem);
	$('#compare'+index).css('display', 'block');
	$('#item'+index).addClass('active');
	curOverItem = index;
}

function overImage(index)
{
	$('.altpop').css('display', 'none');
	$('.altpop').css('opacity', '1');
	//$("div").clearQueue();
	$('#altpop'+index).appendTo('#altpopdisplayer');
	if(curOverItem==index){
		$('#altpop'+index).css('display','block');
	}
	else{
		$('#altpop'+index).delay(200).fadeIn(200);
	}
	$('#altpopdisplayer').css('height', $('#altpop'+index).height()+'px');
}

function outImage(index) {
	$("#altpop"+index).clearQueue();
	$('#altpop'+index).css('display', 'none');
}

function outItem(index)
{
	$('#compare'+index).css('display', 'none');
	$('#item'+index).removeClass('active');
}

function overTab(elem)
{
	$(elem).addClass('tabover');
}

function outTab(elem)
{
	$(elem).removeClass('tabover');
}

function createTabsAction()
{
	$('#tabs_buttons .tab').each(
		function()
		{
			$(this).click(function()
			{
				$('#tabs_buttons .tab').each( function()
				{
					$(this).removeClass('active');
				});
				$(this).addClass('active');
				displayTabs();
				switch($(this).attr('id')) {
					case "technologies" :
						omniture_click(this, "Technologies tab");
						break;
					case "features" :
						omniture_click(this, "Features tab");
						break;
					case "relatednews" :
						omniture_click(this, "related news tab");
						break;
					case "shopfinder" :
						omniture_click(this, "Dealers tab");
						break;
					case "downloads" :
						omniture_click(this, "Manuals tab");
						break;
				}
			});
		}
		);
}

function displayTabs()
{
	$('.tab_content').each(
		function()
		{
			$(this).css('display','none');
		}
		);
	$('#tabs_buttons .tab').each(
		function()
		{
			if( $(this).hasClass('active') )
			{
				id_tab_to_show ='#'+$(this).attr('id')+'_content';
				$( id_tab_to_show ).css('display','block');
			}
		}
		);
	checkContentHeight();
}

function getOutOfMenu()
{
	if(!isTierIphone && !isTierTablet){
		$('#black_screen').add('#top_black_screen').css('display','none');
		if ($.browser.msie && $.browser.version.substr(0,1)<7)
		{
			$('select').css('visibility','visible');
		}
	}
	removeActiveMenu();
}

function hideSubmenu()
{
	$('.submenu').css('display','none');
}

function removeActiveMenu()
{
	$('#menu .active').add('#top_menu div.top_menu_item:not(.last)').removeClass('active');

	hideSubmenu();
}
/*
 * show encart video
 *
 */
function show_video_encart()
{
	$('.right_content_poster').remove();
}
/*
 * select in breadcrumb
 *
 */
function show_hide_select(id)
{
	$(id).slideFadeToggle(250,'easeOutCubic');
}

/*
 * FILTERS BEHAVIOR
 *
 * */
//check for every macromodel if it fits on or several filter set


//check for allready checked filters;
var filtered_list = new Array();

function checkForChecked()
{
	$('.allfilters').each(function () {
		var i = $(this).attr('id');
		if (jQuery.isArray(filters_list[i])){
			var sf;
			if($.cookie(i) != null && $.cookie(i) != '')
			{
				//add to filtered_list
				cv = $.cookie(i);
				sf = cv.split('**');
				filtered_list[i] = sf;
				//select the filter(s)
				for (a in sf){
					filters_list[i][0][sf[a]] = true;
					$('#imgFilter' + sf[a]).css('background-position','0px 6px' );
				}
			}
			if(prefilter && prefilter[i] && prefilter[i].length>0)
			{
				sf = prefilter[i];
				filtered_list[i] = sf;
				for (a in sf){
					filters_list[i][0][sf[a]] = true;
					$('#imgFilter' + sf[a]).css('background-position','0px 6px' );
				}
			}
			show_hide_filterReset(i);
		}
		$(this).hide();
	});
	highlightMacroModels();
//$('#content_gamme_items').html(filters_list.toString());
}
function show_hide_filters(id)
{/*
	$(id).parent().find('.filters_up_down').toggleClass('active');
	$(id).slideFadeToggle(400,'easeOutCubic');
	*/
	$('.allfilters').each(function(u) {
		if (($(this).parent().parent().hasClass('secondline')) == ($(id).parent().parent().hasClass("secondline")))
		{
			$(this).parent().find('.filters_up_down').toggleClass('active');
			$(this).slideFadeToggle(400,'easeOutCubic');
		}
	});
}
jQuery.fn.slideFadeToggle = function(speed, easing, callback) {
	return this.animate({
		opacity: 'toggle', 
		height: 'toggle'
	}, speed, easing, callback);
};

function resetfilters(flId)
{
	for (i in filters_list[flId][0])
	{
		filters_list[flId][0][i] = false;
		$('#imgFilter'+i).css('background-position','-11px 6px' );
		//remove from filtered_list
		filtered_list[flId] = jQuery.grep(filtered_list[flId], function (a) 
		{ 
			return a != i; 
		});
	}
	highlightMacroModels();
	storeFiltered(filtered_list);
	$('#reset_' + flId).hide();
}

function show_hide_filterReset(flId) {
	if ($.isArray(filtered_list[flId]) && filtered_list[flId] != '')
	{
		$('#reset_' + flId).show();
	} else {
		$('#reset_' + flId).hide();
	}
}
var itemsContentGamme,itemsCGp;
function highlightMacroModels_next(){
	var i,j,shown,items = itemsContentGamme;
	itemsCGp.removeClass('last_row_item').removeClass('shown').css({
		display:'none'
	});
	for(j in macromodels) //each macromodel
	{
		highlightMM = true;

		for( i in filters_list )
		{
			if( !isSelectedByFilterList( macromodels[j], i ) )
			{
				highlightMM = false;
				break;
			}
		}
		if(highlightMM)	$('#divProduct'+j).addClass('shown');
	}
	shown = $('.shown',items).css({
		display:'block'
	});
	var max = shown.length,item;
	for(j=3;j<max;j+=4)
		shown.eq(j).addClass('last_row_item');
	items.animate({
		opacity:1
	},300);

	$('.altpop').css('z-index','3');
}
function highlightMacroModels()
{
	if(!itemsContentGamme){
		itemsContentGamme=$('#content_gamme_items');
		itemsCGp=$('.product_item',itemsContentGamme);
	}
	itemsContentGamme.stop(true).animate({
		opacity:0
	},300,highlightMacroModels_next);
}
function removeMtbProd(mtbcfNid, roadcfNid, roadcfRNid)
{
	for(j in macromodels) //each macromodel
	{
		if (!macromodels[j][mtbcfNid]){
			macromodels[j][roadcfNid] = true;
		}
	}
	checkUncheckButtons(roadcfNid, roadcfRNid);

}
function isSelectedByFilterList(macromodel, flId )
{
	ret = false;
	oneFilterSet = false;
	for(i in filters_list[flId][0])//each filter
	{
		if( filters_list[flId][0][i]  == true )//filter checked
		{
			oneFilterSet = true;
			if( macromodel[ i ] == true )
			{
				ret = true;
				break;
			}
		}
	}
	if(oneFilterSet)	return ret;
	else return true;
}


//check or uncheck a button
function checkUncheckButtons(id, flId)
{
	//check or uncheck the filter
	if( filters_list[flId][0][id] )
	{
		filters_list[flId][0][id] = false;
		$('#imgFilter'+id).css('background-position','-11px 6px' );
		//remove from filtered_list
		filtered_list[flId] = jQuery.grep(filtered_list[flId], function (a) 
		{ 
			return a != id; 
		});
	}
	else
	{
		filters_list[flId][0][id] = true;
		$('#imgFilter'+id).css('background-position','0px 6px' );
		//add to filtered_list
		if(jQuery.isArray(filtered_list[flId]))
		{
			filtered_list[flId].push(id);
		}
		else
		{
			filtered_list[flId] = new Array(id);
		}

	}

	if( filters_list[flId][1] == 'radiobox') //uncheck all other buttons
	{
		for( var i in filters_list[flId][0] )
		{
			if(i!=id)
			{
				filters_list[flId][0][i] = false;
				$('#imgFilter'+i).css('background-position','-11px 6px' );
				//remove from filtered_list
				filtered_list[flId] = $.grep(filtered_list[flId], function (a) 
				{ 
					return a != i; 
				});

			}
		}
	}
	storeFiltered(filtered_list);
	show_hide_filterReset(flId);
}
//store the selected filters in a cookie for each filtertype
function storeFiltered(a)
{
	for(i in a)//each filter
	{
		var cv = a[i].join('**');
		jQuery.cookie(i, cv,  {
			path: '/', 
			expires: 10
		});
	}
}

/*
 * MACRO MODEL ZOOM AND MEGAZOOM
 * */
function showZoom(url,title)
{
	$('html, body').animate({
		scrollTop: 0
	}, 0);

	if(isTierIphone || isTierTablet){
		$('#top_menu').add('#logo_container').add('#container').hide();
	}
	
	$('select, #player').css('visibility','hidden');//hide videoplayer and select for IE bug

	$("#megaZoom").hide();
	
	//$('#zoomBoxImg').attr("src",url);
	var zoomBox = $('#zoomBox').empty();
	var img = new Image();
	$(img)
	// once the image has loaded, execute this code
	.load(function () {
		// set the image hidden by default
		//$(this).hide();
		zoomBox.empty();
		zoomBox.append(this);
		// fade our image in to create a nice effect
		var windW =$(window).width(), windH = $(window).height();
		

		if( windW > windH )
		{
			this.width=this.height=windH;
	    	  
		}
		else
		{
			this.width=this.height=windW;
		}
	      
		zoomBox.css('z-index','101' );
	});

	// *finally*, set the src attribute of the new image to our image
	$(img).attr('src', url);
	
	if($.browser == 'webkit'){
		zoomBox.css({
			height:windH,
			width:windW,
			zIndex:101
		});
	} else {
		zoomBox.css({
			height:'100%',
			width:'100%',
			zIndex:101
		});
	}

	if(url.indexOf('_black.jpg')!=-1)
	{
		zoomBox.css('background','#000000');
	}
	else
	{
		zoomBox.css('background','#FFFFFF');
	}
	$(window).bind("resize", function(){
		zoomBox.ezBgResize();
		zoomBox.css('z-index','101' );
	});
	$("#zoomBox, #closeZoom_wrapper").appendTo('body').show();
	
	$('body').css({
		"overflow":"hidden"
	});
	
	showNavigationBox();
	
	showTitleBox(title);
	
	$('#footer').add('#top_menu').hide();
	
	
//
// omniture event 
//
	
}

function showTitleBox(title)
{
	
	if ( $("#title_box_zoom").length < 1 ) 
	{		
		var title_box_zoom = $("<div id='title_box_zoom'></div>").appendTo("body");		
		title_box_zoom.css({
			position : 'absolute',
			zIndex : '20001',
			top : '22px',
			left : '22px',
			'padding-left' : '12px',
			'padding-right' : '12px',
			height : '40px',
			'background-color' : '#ffffff'
		});
		//Add text:
		$("<p id='title_text' class='helvetica'>"+title+"</p>").appendTo(title_box_zoom);		
		Cufon.refresh('.helvetica');
	}
	else
	{
		//change text:
		$("#title_box_zoom #title_text").html(title);	
		Cufon.refresh('.helvetica');
	}
	
	$("#title_box_zoom").show();
}


function showNavigationBox()
{
	if ( $("#navigation_box_zoom").length < 1 ) 
	{		
		var navigation_box_zoom = $("<div id='navigation_box_zoom'></div>").appendTo("body");
		
		var tmp = $("#navigation_box").clone();
		tmp.html(tmp.html().replace(/_wheel"/gi,'_wheel_z"'));
		tmp.appendTo(navigation_box_zoom);
	
		navigation_box_zoom.css({
			position:'absolute',
			zIndex:'20000',
			bottom:'60px',
			left:'30px',
			backgroundColor:'#fff'
		});
		
		
		$('#navigation_box_zoom .feature-image').mousemove(function(e){
			$("#altpopdisplayer").show();
			
			posX = e.pageX +15;
			posY = e.pageY ;
			if(posX > 800){
				posX=posX-270;
			}

		
			$("#altpopdisplayer").css({
				top: (posY) + "px",
				left: (posX) + "px"
			});
		}).mouseout(function(e){
			//mouse move in product page for technologies
			$("#altpopdisplayer").hide();
		});
		
		createChangeColor();
		createChangeView();
		createShowTechnologie();
	}
	
	$("#navigation_box_zoom").show();
}

function createShowTechnologie()
{
	$('#navigation_box_zoom a.feature-image').click(function() 
	{
		showZoom(this.href, $(this).find('img').attr('alt'));
		return false;
	});
	
	$('#navigation_box_zoom a.feature-image-notooltip').click(function() 
	{
		showZoom(this.href, $(this).find('img').attr('alt'));
		return false;
	});

}

function createChangeView()
{// vincent revien la !
	$('#navigation_box_zoom a.button_view').click(function() {
		$(this).parent().children().removeClass('button-view-active button-view-rear-active button-view-front-active');
	});			
	
	$('#front_wheel_z').click(function() {
		//Get article/color of active image
		
		$('#front_wheel').click();
		$(this).addClass('button-view-active button-view-front-active');
		var $visibleSrc = $('#product img.visuel:visible').attr("src");
		var $visibleTitle = $('#product img.visuel:visible').attr("alt");

		showZoom($visibleSrc.replace('normal', 'zoom'),$visibleTitle);	
	});
	$('#rear_wheel_z').click(function() {
	
		$('#rear_wheel').click();
	
		$(this).addClass('button-view-active button-view-rear-active');
		var $visibleSrc = $('#product img.visuel:visible').attr("src");
		var $visibleTitle = $('#product img.visuel:visible').attr("alt");

		showZoom($visibleSrc.replace('normal', 'zoom'),$visibleTitle);	
	});
	$('#alt2_wheel_z').click(function() {
	
		$('#alt2_wheel').click();
	
		$(this).addClass('button-view-active button-view-rear-active');
		var $visibleSrc = $('#product img.visuel:visible').attr("src");
		var $visibleTitle = $('#product img.visuel:visible').attr("alt");

		showZoom($visibleSrc.replace('normal', 'zoom'),$visibleTitle);	
	});
}

function createChangeColor()
{
	$('#navigation_box_zoom #change_color a').click(function() {
		//change opacity for others
		$('#navigation_box_zoom .change-color').css('opacity','1');
		$(this).css('opacity','0.3');				
		
		//		$('#change_view a.button_view').show();//Link to change view available
		var articleId = $(this).children(":first").attr("name");
		var articleName =  $(this).attr("name");
		var articleAlt =  $(this).find("img").attr("alt");
		
		$('#product .change-color[name="'+ articleName +'"]').click();
		
				
		$('#zoom').attr("href", $('#product img.visuel-'+articleId).attr("href"));		
		
		$color_href = $("#zoom").attr("href");

		showZoom($color_href.replace('normal', 'zoom'),articleAlt);

		return false;
	});
}



function omniture_click(obj, event) {
	var s2=s_gi(s_account);
	s2.linkTrackVars='prop33';
	s2.prop33=event;
	s2.tl(obj,'o',event);
	return true;
}

function omniture_click_filter(obj, event) {
	var s2=s_gi(s_account);
	s2.linkTrackVars='prop32';
	s2.prop32=event;
	s2.tl(obj,'o',event);
	return true;
}

function omniture_click_encart(obj, event) {
	var s2=s_gi(s_account);
	s2.linkTrackVars='eVar4';
	s2.eVar4=event;
	s2.tl(obj,'o',event);
	return true;
}

function hideZoom()
{
	$("#zoomBox, #closeZoom_wrapper").hide();
	$('select, #player').css('visibility','visible');//hide videoplayer and select for IE bug
	$("#navigation_box_zoom").hide();
	$("#title_box_zoom").hide();
	$('#footer').add('#top_menu').show();
	$('body').css({
		"overflow":"auto"
	});
	if(isTierIphone || isTierTablet){
		$('#top_menu').add('#logo_container').add('#container').show();
	}


}

function showMegaZoom()
{
	if(isTierIphone || isTierTablet){
		$("#megaZoom").css({
			left: 0, 
			top: 0
		});
		$('#zoombox').hide();
	} else {
		$("#megaZoom").unbind("mousemove").mousemove(function(e){
			var divWidth = ($.browser == 'webkit')?$('body').width():$(window).width();
			var divHeight = ($.browser == 'webkit')?$('body').height():$(window).height();
			var igW = $("#megaZoom").width();
			var igH = $("#megaZoom").height();
			var leftPan = (e.pageX ) * (divWidth - igW) / (divWidth);
			var topPan = (e.pageY ) * (divHeight - igH) / (divHeight);
	
			$("#megaZoom").css({
				left: leftPan, 
				top: topPan
			});
		});
	}
	$('#megaZoomImg').attr("src", $('#zoomBox img').attr('src').replace('zoom','megazoom'));
	
		
	if($('#megaZoomImg').attr("src").indexOf('_black.jpg')!=-1)
	{
		$('#megaZoom').css('background','#000000');
	}
	else
	{
		$('#megaZoom').css('background','#FFFFFF');
	}
	$("#megaZoom, #closeZoom_wrapper").appendTo('body').show();
}

function hideMegaZoom()
{

	$("#megaZoom").hide();
	if(isTierIphone || isTierTablet){
		$('#zoombox').show();
	}
	
}


/************************************************

PRODUCT COMPARE FUNCTIONS
**************************************************/
//used in node-family.tpl.php
var productCompare = new Array();
var data = null;
function storeToCompare(id)
{
	$('#product_compare_block').css('display','block');
	i = $.inArray(id, productCompare);
	if( i != -1 )
	{
		productCompare.splice(i,1);
		$('#imgCompare'+id).attr('src','/sites/default/themes/mavic/images/select_to_compare_off.gif');
	}
	else
	{
		productCompare.unshift( id );
		$('#imgCompare'+id).attr('src','/sites/default/themes/mavic/images/select_to_compare_on.gif');
	}

	while(productCompare.length > 3 )
	{
		index = productCompare[productCompare.length-1]
		$('#imgCompare'+index).attr('src','/sites/default/themes/mavic/images/select_to_compare_off.gif');
		productCompare.pop();
	}

	//update session
	$.cookie('product_compare_'+activeFamily, productCompare.join('-'), {
		path: '/', 
		expires: 10
	} );

	if( productCompare.length <= 0 ){
		$('#product_compare_block').css('display','none');
	}
	$('#product_compare_title').html(t_compare+' ('+productCompare.length+')');

}

function storeOneProductToCompare(nid)
{
	i = $.inArray(nid, productCompare);
	if( i != -1 )
	{
		productCompare.splice(i,1);
	}
	productCompare.unshift( nid );

	while(productCompare.length > 3 )
	{
		index = productCompare[productCompare.length-1]
		productCompare.pop();
	}
    
	//update session
	$.cookie('product_compare_'+activeFamily, productCompare.join('-'), {
		path: '/', 
		expires: 10
	} );
    
	openProductCompare();
}

function getSessionProductCompare(firstNid)
{
	data = $.cookie('product_compare_'+activeFamily);
	if (data == null || data == '') {
		productCompare[0] = firstNid;
	} else {
		productCompare = data.split('-');
	}
}

/* call product compare*/
function openProductCompare()
{
	bgi=0;
	$(".compareBit").remove();

	$('#productCompareContainer').css({
		'display':'block',
		'background':'white'
	});

	$('#loader').css({
		'display':'block', 
		'height':$('#black_screen').height()+'px'
		});

	$('#menu').css('visibility','hidden');
	if($('#logo_container').css('left')=='15px'){
		$('#logo_container').css('display','none');
		$('#player').css('display','none');
	}
	setTimeout("pcShowProducts()",10);
}
function pcShowProducts()
{
	// Get the three products
	var complete = productCompare.length;
	var products = [];
	for (i=0;i<complete;i++) {
		$.ajax({
			url: basePath+'/productcompare/'+productCompare[i],
			complete: function(data) {
				products.push(data.responseText);
				complete -= 1;
				if (complete == 0) storeProductToHtml(products);
			}
		});
	}
}

function storeProductToHtml(products) {
	// Build HTML from AJAX response
	var container = $("#productCompareContainer");
	for(var i = 0; i < products.length; i++) {
		var product = $('<div></div>');
		product.addClass('compareBit');
		product.html(products[i]);
		product.appendTo(container);
	}
    
	pcCreateImgeRow();//specific row creation for imageand select first row
	if (data != null && data != '') {
		$('.pcblockimage').each(
			function()
			{
				column = $(this).attr('data-title');
				$(this).appendTo('#pc_image'+column);
			});

		$('.pcblockweight').each(
			function()
			{
				column = $(this).attr('data-title');
				if( $('#pc_weight').length==0){
					pcCreateFeatureRow('pc_weight', 0);
				}
				$(this).appendTo('#pc_weight'+column);
			});

		$('.pcblockfeatures').each(
			function()
			{
				ttitle = $(this).attr('data-title').split('|');
				featureType= ttitle[0];
				column = ttitle[1];
				order= ttitle[2];
				if( $('#'+featureType).length==0){
					pcCreateFeatureRow(featureType, order);
				}
				//$(this).appendTo('#'+featureType+column);
				$('#'+featureType+column).html($(this).html());
			});

		$('.pcblocktechnos').each(
			function()
			{
				column = $(this).attr('data-title');
				if( $('#pc_technologies').length==0){
					pcCreateFeatureRow('pc_technologies', 100);
				}
				$(this).appendTo('#pc_technologies'+column);
			});

		bgi=0;
		for(i in tRows)
		{
			tRows[i].appendTo('#productCompareTable');
			if(bgi%2 != 0){
				tRows[i].addClass('odd');
			}
			bgi++;
		}
	}
	$('#main_content').css('visibility','hidden');
	$('#loader').hide();
	$('#productCompareTable').fadeIn();
	positionLogo();
}

var tRows = new Array();

function pcCreateFeatureRow(featureType, order)
{
	row = $('<tr></tr>');
	row.attr('id',featureType);
	

	for(i in productCompare)
	{
		col = $('<td valign="top"></td>');
		col.attr('id',featureType+productCompare[i]);
		col.attr('valign','top');
		col.appendTo(row);
	}
	row.appendTo('#productCompareTable')
	tRows[order]=row;
}

function pcCreateImgeRow()
{
	row = $('<tr></tr>');
	row.attr('id','pc_image');

	if (data != null && data != '') {
		for(i in productCompare)
		{
			col = $('<td valign="top" align="left"></td>');
			col.attr('id','pc_image'+productCompare[i]);
			col.attr('valign','top');
			col.appendTo(row);
		}
	}
	//fill empty cells
	if (data == null || data == '')
	{
		col = $('<td valign="top" align="left"></td>');
		col.attr('id','pc_image0');
		col.attr('valign','top');

		col.html('<img src="/sites/default/themes/mavic/images/pc_noimage.jpg"/>');
		select= $('<select onchange="updateProductCompare(this,this.options[this.selectedIndex].value);" ></select>');
		select.attr('id','pc_select_0');
		select.addClass('pc_select_empty');
		select.html($('.pc_select_page').html());
		select.appendTo(col);
		col.appendTo(row);
	}
	if(productCompare.length < 2 )
	{
		col = $('<td valign="top" align="left"></td>');
		col.attr('id','pc_image1');
		col.attr('valign','top');

		col.html('<img src="/sites/default/themes/mavic/images/pc_noimage.jpg"/>');
		select= $('<select onchange="updateProductCompare(this,this.options[this.selectedIndex].value);" ></select>');
		select.attr('id','pc_select_1');
		select.addClass('pc_select_empty');
		select.html($('.pc_select_page').html());
		select.appendTo(col);
		col.appendTo(row);
	}
	if(productCompare.length < 3 )
	{
		col = $('<td valign="top" align="left"></td>');
		col.attr('id','pc_image2');
		col.attr('valign','top');

		col.html('<img src="/sites/default/themes/mavic/images/pc_noimage.jpg"/>');
		select= $('<select onchange="updateProductCompare(this,this.options[this.selectedIndex].value);" ></select>');
		select.attr('id','pc_select_2');
		select.addClass('pc_select_empty');
		select.html($('.pc_select_page').html());
		select.appendTo(col);
		col.appendTo(row);
	}
	row.appendTo('#productCompareTable');
	$('.pc_select_empty').each(function (){
		this.selectedIndex=0
	});
        
	// remove -- select product -- on first list
	if (productCompare.length <= 1) {
		$('.pc_select_page option[value=\'0\']').first().remove();
	}
    
	//disable option contained in productcompare table
	if (data != null && data != '') {
		$('.pc_select_page option, .pc_select_empty option').each(function(){
			i=$.inArray(this.value, productCompare);
			if(i!=-1){
				$(this).attr('disabled','disabled');
			}
		});
	}
    
	// set custom SELECT BOX
	$('.pc_select_page, .pc_select_empty').attr('style', 'width:305px;').msDropDown();
                
	// hide all descriptions and images
	$("div.dd").find("span.description").hide();
	$(".dd .ddChild li").find("img").hide();

	// on hover on each element : show/hide description+image
	$(".ddChild li").hover(
		function(){
			$(this).find("img").show();
			$(this).find("span.description").show();
		},
		function () {
			$(this).find("img").hide();
			$(this).find("span.description").hide();
		});
}

function updateProductCompare(elt, newNid)
{
	oldNid = $('#'+elt.getAttribute('id')).parent().parent().attr('data-title');
	if (data != null && data != '') {
		//test if new id already in product compare (IE6 does not support disabled option)
		i = $.inArray(newNid, productCompare);
		if(i!=-1){
			return false;
		}
	}

	if(newNid==0)//remove the nid from productcompare
	{
		i = $.inArray(oldNid, productCompare);
		if(i!=-1){
			productCompare.splice(i,1);
		}
	}
	else if(data != null && data != '')
	{
		i = $.inArray(oldNid, productCompare);
		if(i!=-1){
			productCompare[i]=newNid;
		}
		else{
			productCompare[productCompare.length]=newNid;
		}
	} else {
		productCompare[0]=newNid;
	}
        
	//switch off the old nid
	//outItem(oldNid);

	//switch on the new nid
	//overItem(newNid);
    
	$('#productCompareTable').hide();
	$('#loader').css({
		'display':'block'
	});
	$('#productCompareTable').empty();

	//update session
	$.cookie('product_compare_'+activeFamily, productCompare.join('-'), {
		path: '/', 
		expires: 10
	} );
	data = $.cookie('product_compare_'+activeFamily);
    
	//$('#product_compare_title').html('COMPARE ('+productCompare.length+')');
	setTimeout('openProductCompare()',10);
        
    
	return false;
}

function closeProductCompare()
{
	$('#productCompareContainer').css('display','none');
	$('#menu').css('visibility','visible');
	$('#main_content').css('visibility','visible');

	positionLogo();

	$('#productCompareTable').empty();
	$('#logo_container').css('display','block');
	$('#player').css('display','block');
}


/*****************************
HOMEPAGE FUNCTIONS
******************************/
function repositionDescription()
{
	var tmp;
	width = $('#img_'+slides[activeSlide]).width();
	width = $('#img_'+slides[activeSlide]).width();
	switch(slides[activeSlide])
	{
		case 'ecard2013vtt':
			right = Math.floor(width/2);
			$('#homedescription_ecard2013vtt').css({
				right:right+'px',
				left:''
			});
			break;
		case 'ecard2013road':
			right = Math.floor(width/2);
			$('#homedescription_ecard2013road').css({
				right:right+'px',
				left:''
			});
			break;
		case 'ironman':
			right = Math.floor(width*2/3.8);
			$('#homedescription_ironman').css({
				right:right+'px',
				left:''
			});
			break;
		case 'TDF_2012':
			left = Math.floor(width*2/3.8);
			$('#homedescription_TDF_2012').css('left',left + 'px');
			break;
		case 'cxr':
			right = Math.floor(width-(width/4.5));
			$('#homedescription_cxr').css({
				right:right+'px',
				left:''
			});
			break;
		case 'cc40c':
			right = Math.floor(width-(width/6.2));
			$('#homedescription_cc40c').css({
				right:right+'px',
				left:''
			});
			break;
		case 'wheel1':
			right = Math.floor(width-(width/3.8));
			$('#homedescription_wheel1').css({
				right:right+'px',
				left:''
			});

			break;
		case 'mp3':
			if (language == "fr"){
				$('#homedescription_mp3 h1').css('font-size','22px');
			}
			if (width/2 > 420){
				right = width - 420;
			} else {
				right = width/2
				}
			$('#homedescription_mp3').css({
				right:right+'px',
				left:''
			});
			break;
		case 'tyre2012':
			right = Math.floor(width-(width/8));
			$('#homedescription_tyre2012').css({
				right:right+'px',
				left:''
			});
			break;
		case 'wheel2':
			right = Math.floor(width-(width/8));
			$('#homedescription_wheel2').css({
				right:right+'px',
				left:''
			});
			break;
		case 'pant':
			right = Math.floor(width-(width/5));
			$('#homedescription_pant').css({
				right:right+'px',
				left:''
			});
			break;
		case 'computer':
			left = Math.floor(width/2);
			$('#img_computer').css({
				marginLeft:-left+'px'
				});
			break;
		case 'contest':
			left = Math.floor(width/4.2);
			//$('#img_pedal').css({marginLeft:-left+'px'});
			$('#homedescription_contest').css('left',left + 'px');
			break;
		case 'enduro_barel':
			left = Math.floor(width/1.8);
			//$('#img_pedal').css({marginLeft:-left+'px'});
			$('#homedescription_enduro_barel').css('left',left + 'px');
			break;
		case 'giro_home':
			left = Math.floor(width*2/3);
			$('#homedescription_giro_home').css('left',left + 'px');
			break;
		case 'apparel2013mtb':
			left = Math.floor(width/2);
			$('#img_apparel2013mtb').css({
				marginLeft:-left+'px'
				});
			break;
		case 'apparel2013road':
			left = Math.floor(width/2);
			$('#img_apparel2013road').css({
				marginLeft:-left+'px'
				});
			break;
		case 'helmet':
			left = Math.floor(width/2);
			$('#img_helmet').css({
				marginLeft:-left+'px'
				});
			break;
		case 'footwear':
			left = Math.floor(width/2);
			$('#img_footwear').css({
				marginLeft:-left+'px'
				});
			break;
		case 'apparel2011-1':
		case 'apparel2012':
			right = Math.floor(width-(width/4.2));
			tmp=$('#homedescription_'+slides[activeSlide]).css({
				right:right+'px',
				left:''
			});
			$('h1 span.nextline canvas,h1 span.nextline cufoncanvas',tmp).css({
				top:'-9px'
			});	
			break;
		case 'apparel2011-2':
			left = Math.floor(width*.75)+$('#img_'+slides[activeSlide]).position().left;
			tmp=$('#homedescription_apparel2011-2').css({
				left:left+'px'
				});
			$('h1 span.nextline canvas,h1 span.nextline cufoncanvas',tmp).css({
				top:'-9px'
			});
			break;
		case 'roubaix':
			right = Math.floor(width-(width/9));
			$('#homedescription_roubaix').css({
				right:right+'px',
				left:'', 
				top: ''
			});
			break;
		case 'crossmax29':
			right = Math.floor(width-(width/3.5));
			$('#homedescription_crossmax29').css({
				right:right+'px', 
				left:''
			});
			break;
		case 'london':
			$('#homedescription_london').css({
				right:'', 
				left:'66%'
			});
			break;
	}

	
	if($('#homedescription_'+slides[activeSlide]).length>0)
	{
		p=$('#homedescription_'+slides[activeSlide]).position().left;
		if(p< 250)
		{
			$('#homedescription_'+slides[activeSlide]).css('left','250px');
		}
	}
}


function homeShowSlide(id)
{
	$("#homeslide_"+slides[oldSlide]).css('display','none');

	oldSlide = activeSlide;
	activeSlide = id;
	$("#homeslide_"+slides[oldSlide]).stop(true,true);

	$("#homeslide_"+slides[id]).appendTo('#fixed_slider');
	$("#homeslide_"+slides[id]).css('opacity','0');
	$("#homeslide_"+slides[id]).css('left','100px');
	$("#homeslide_"+slides[id]).css('display','block');


	$("#homeslide_"+slides[id]).animate({
		opacity: 1,
		left: '0'
	},500,'easeOutCubic');


	$("#homeslide_"+slides[id]).ezBgResize();
	repositionDescription();
	homeCheckSize();
	$("#homeslide_"+slides[id]).css('z-index','2');
	$('.homebutton').css('background','#FFE500');
	$("#homebutton_"+id).css('background', $("#homedescription_"+slides[id]).css('color'));



}

function homeCheckSize()
{
	//handle homeslide on homepage
	$(".homeslide").css('z-index','-1');
	$("#homeslide_"+slides[activeSlide]).css('z-index','2');

	if(!isTierIphone && !isTierTablet) {
		$("#homeslide_"+slides[activeSlide]).ezBgResize();


		
		if( $(window).height()<750  )
		{
			$("#container").css('height','750px');
		}
		else
		{
			$("#container").css('height',$(window).height()-$('#footer').height());
		}

		if( $(window).width()<1024  )
		{
			$("#container").css('width','1024px');
		}
		else
		{
			$("#container").css('width',$(window).width());
		}
	} else {

		
		if( $(window).height() < $(window).width()  )
		{
			
			$("#homeslide_"+slides[activeSlide]).css('width','1024px');
			$("#container").add('body').add('#footer').css('width',$('#body-background').width());
		}
		else
		{
			$("#homeslide_"+slides[activeSlide]).css('width','991px');
			$("#container").add('body').css('width',$('#body-background').width());
		}
	}

}


function homeAutoDefil()
{
	var totSl = $('.homeslide').length;
	nxt = activeSlide+1;
	if(nxt>totSl)nxt=0;
	homeShowSlide(nxt);
	homeTimer = setTimeout("homeAutoDefil()",6000);
}


/* Uncomment this code to be notified of playback errors in JavaScript:*/
function onMediaPlaybackError(playerId, code, message, detail)
{
	alert(playerId + "\n\n" + code + "\n" + message + "\n" + detail);
}

/*NEWS functions */
function showMoreNews( n )
{
	for(i=0;i<n;i++)$('#list_nextnews .news-preview:first').appendTo($('#list_morenews'));
//	$('#list_morenews').animate({height: '+=120'} , 500);
}

/* ************************
 *roll over rouge dans sous menu mavic 
 */
function switch_menu_color_red (menu_id) {
	black = document.getElementById(menu_id);
	red = document.getElementById(menu_id+"_red");
	black.style.display = 'none';
	red.style.display = 'block';
}

function switch_menu_color_black (menu_id) {
	black = document.getElementById(menu_id);
	red = document.getElementById(menu_id+"_red");
	red.style.display = 'none';
	black.style.display = 'block';
}

function skinSelect(name){
	
	var obj= $("#"+name), first=obj.children('option').eq(0);
	obj.css({
		opacity: 0
	}).wrap('<div class="customselect-wrap"></div>').before('<div class="customselect-text">'+first.css({
		color:"#808080"
	}).html()+'</div>').change(function(){
		$(this).prev().html(this.options[this.selectedIndex].innerHTML).css({
			color:(this.selectedIndex==0)?"#808080":"#000"
			});
	}).keyup(function(){
		$(this).prev().html(this.options[this.selectedIndex].innerHTML).css({
			color:(this.selectedIndex==0)?"#808080":"#000"
			});
	}).focus(function(){
		$(this).prev().addClass("customselect-focus");
	}).blur(function(){
		$(this).prev().removeClass("customselect-focus");
	});
	
}

/* ************************
 *newsletter subscription pop-up
 */
function popupnewslettersubscript(){
	if ( $('#nlfirstvisit').length > 0){
		var footerheight = $('#footer').height() - 12;
		var nlv = $('#nlfirstvisit').css({
			bottom:footerheight
		}).slideDown(2000).delay(7000).slideUp(2000);
	};
}
/* ************************
 *New range landing page (helmet, crossmax... but not ss2011)
 *
 */
var nextTimeout, nextClima, navover=false, navt=1, currentDiapo=0, currentDiapo2;
var landing_init = function(target,navItem){
	var body, diapos, diapo, diapolength, max, nav;
	$('#main_content').css({
		'padding':'0 0 5px 0',
		'margin':0,
		width:'741px'
	});
	$('#subcontainer').css({
		width:'981px'
	});
	
	diapo = $("."+target);
	diapolength = diapo.length;
	diapos = $('#'+target+"s").css({
		'width': (diapolength * 741) + 'px'
		});//741*3
	diapo.each(function (i) {
		$(this).css({
			'z-index':i,
			marginLeft:'741px'
		});
	});
	max = diapolength-1;
	diapo.eq(0).animate({
		marginLeft:0
	},500,"easeOutCubic");
	switch (target){
		case 'diapo':
			$('.techno_desc').each(function () {
				$(this).ellipsis();
			});
			nav = $("."+navItem).click(function(e){
				if(e.pageX){
					clearTimeout(nextTimeout);
				}
				if($(this).hasClass("nav-left")){		
					diapo.each( function(){
						$(this).stop(true);
					});
					diapo.eq(currentDiapo).animate({
						marginLeft:'741px'
					},500,"easeOutCubic");
					if(currentDiapo==0){
						currentDiapo = max+1;			
					}
					diapo.eq(currentDiapo-1).css({
						marginLeft:'-741px'
					}).animate({
						marginLeft:0
					},500,"easeOutCubic");
					currentDiapo--;
				}
				else{
					diapo.eq(currentDiapo).animate({
						marginLeft:'-741px'
					},500,"easeOutCubic");
					if(currentDiapo==max){
						currentDiapo = -1;			
					}
					diapo.eq(currentDiapo+1).css({
						marginLeft:'741px'
					}).animate({
						marginLeft:0
					},500,"easeOutCubic");
					currentDiapo++;
				}
			});
			nav.css({
				display:"none"
			});
			var diaporamaMousemove = function(e){
				diapo = $("."+target);
				if (diapo.eq(currentDiapo).hasClass('diapo_video_block')) {
					if(e.pageX<1000 && e.pageX>240 && e.pageY<490){
						nav.stop(true).removeAttr("style");
					}
					else{
						nav.stop(true).css({
							display:'none'
						});
					}
			
				} else {
					if(e.pageX<600 && e.pageX>245 && e.pageY<490){
						nav.eq(0).stop(true).removeAttr("style");
						nav.eq(1).stop(true).css({
							display:'none'
						});
					}
					else if(e.pageX>640 && e.pageX<995 && e.pageY<490){
						nav.eq(1).stop(true).removeAttr("style");
						nav.eq(0).stop(true).css({
							display:'none'
						});
						navover=true;
					}
					else{
						nav.stop(true).css({
							display:'none'
						});
					}
				}
			};
			body = $('body').mousemove(diaporamaMousemove);
			discoBcStyle = $('#disco_bc').attr('style');
			$('#discover').hover(
				function(){
					$('#disco_title').css('background-position','0 -139px');
					$('#disco_bc').css('background','#FFE500');
				},
				function(){
					$('#disco_title').css('background-position','0 0');
					$('#disco_bc').attr('style',discoBcStyle);
				}
				);	
			nextTimeout = setTimeout(function(){
				nextSlide(navover,nav);
			},6500);
			break;
		case 'clima_ride_block':
			currentDiapo2 = 0;
		
			nav = $("."+navItem).click(function(e){
				clearTimeout(nextClima);
				var navi = nav.index($(this));
				nav.removeClass('active');
				$(this).addClass('active');
				goToNext(navi,diapo);
			}).hover(function(e){
				$(this).toggleClass('hover');
			});
			nextClima = setTimeout(function(){
				goToNextOne(navt,diapo,navItem);
			},6500);
		
			break;
		default:
			
			break;
	
	}
	
};
var goToNextOne = function(navt, diapo, navItem){
	$("."+navItem).each(function(){
		$(this).removeClass('active');
	});
	$("."+navItem).eq(navt).addClass('active');
	goToNext(navt, diapo);
	if(navt == 4){
		navt = 0;
	} else {
		navt++;
	};
	nextClima = setTimeout(function(){
		goToNextOne(navt, diapo, navItem);
	},4500);
};
var goToNext = function(navi,diapo){
	if(navi < currentDiapo2){		
		diapo.eq(currentDiapo2).animate({
			marginLeft:'741px'
		},500,"easeOutCubic");
		if(currentDiapo2 - navi == 1){
			diapo.eq(currentDiapo2-1).css({
				marginLeft:'-741px'
			}).animate({
				marginLeft:0
			},500,"easeOutCubic");			
		} else {
			diapo.eq(currentDiapo2-1).css({
				marginLeft:'-741px'
			});
		}
		currentDiapo2--;
		goToNext(navi,diapo);
	};
	if (navi > currentDiapo2) {
		diapo.eq(currentDiapo2).animate({
			marginLeft:'-741px'
		},500,"easeOutCubic");
		if(navi - currentDiapo2 == 1){
			diapo.eq(currentDiapo2+1).css({
				marginLeft:'741px'
			}).animate({
				marginLeft:0
			},500,"easeOutCubic");			
		}
		diapo.eq(currentDiapo2+1).css({
			marginLeft:'741px'
		});
		currentDiapo2++;
		goToNext(navi,diapo);
	};
};
var nextSlide = function(navover,nav){
	!navover?nav.eq(1).fadeIn(300,function(){
		nav.eq(1).fadeOut(500,function(){
			nav.eq(1).css({
				display:'none'
			});
		});
	}):0;
	nav.eq(1).click();
	nextTimeout = setTimeout(function(){
		nextSlide(navover,nav);
	},6000);
};

var showVideo = function(){
	$('.poster_video').remove();
	clearTimeout(nextTimeout);
};
var showSmallVideo = function(targetId){ 
	$("#vThumb_"+targetId).remove();
};

function rangeOtherColor(productNid, colorId) {
	// Change product picture
	$('#productImg'+productNid).find('img').hide();
	$('#range'+colorId).fadeIn();
	// Highligh the selected color
	var currentColor = $('#alter-range-'+colorId);
	currentColor.parent('div.alter-range-choice').find('img').attr('class', 'alter-range-img');
	currentColor.addClass('selected');
    
	// Change URL to go to the selected color on product page
	var url = $('#productImg'+productNid).attr('alt');
	$('#productImg'+productNid).click(function() {
		document.location.href=url+'#'+colorId;
	});
}