jQuery(document).ready(function($) {
    /**
     * Overlay Main menu 
     */
	/* ALEXIS: Added responsive size */
if ($('#page').is('.mobile') || $(window).width() <= 740) {
   /* ALEXIS : Scale #zone-menu-wrapper to obtain proper proportions for background image*/
   resizeZoneMenuWrapper();

   $(window).resize(function(){
	   resizeZoneMenuWrapper();
   });
   
   // Creation du bloc menu filter
   $('.view-filters .views-exposed-form').prepend('<div class="search_filter_block"><p class="search_filter">Filter your search</p></div>');
   
   $('.block-listing-produits .view-filters .views-exposed-widgets').hide();
   
   $(".search_filter_block .search_filter").click(function() {
        $(this).toggleClass('active');
        $('.block-listing-produits .view-filters .views-exposed-widgets').slideToggle("slow");
        $('.block-listing-produits .view-filters ul.bef-tree ul.bef-tree-depth-2').hide();
    }
    );
    $(".block-listing-produits .view-filters ul.bef-tree li.depth-1").click(function() {
        $(this).toggleClass('active');
        $(this).find('ul').slideToggle("slow");
    });

    // Barre Google search
    $('#block-google-cse-google-cse').click(function() {
        $('#region-menu ul#superfish-1').toggleClass('googlecse-active');
        $('#google-cse-results-searchbox-form').toggle();
    });
    
    // Main menu
    $('#superfish-1 > li.sf-depth-1').click(function() {
        $(this).toggleClass('active');
        $(this).find('ul').slideToggle("slow");
        return false;
    });
    
    $('#superfish-1 .sf-megamenu-column').click(function() {
        $(this).toggleClass('active');
        $(this).find('ol').slideToggle("slow");
        return false;
    });    
    
    

}
else {
    $(".block-superfish-1 ul#superfish-1 > li").mouseover(function() {
        var t_pt_zm = $("#zone-menu").css("padding-top");
        var t_rm = $("#region-menu").height();
        var t_pb_sfm = $(".sf-megamenu").css("padding-bottom");
        var t_hover = $(".sf-megamenu").height();
        var total_h = parseInt(parseFloat(t_pt_zm)) + parseInt(parseFloat(t_rm)) + parseInt(parseFloat(t_pb_sfm)) + parseInt(parseFloat(t_hover)) + 20;
        var body_h = $('body').height();
        var body_top = total_h + 'px';
        
        $('#zone-menu').before('<div id="bloc-menu-hover"> </div>');
        var bloc_overlay = '<div id="overlay_menu">&nbsp;</div>';

        $('body').append(bloc_overlay);
        $("#bloc-menu-hover").css({'height': total_h});
        $("#overlay_menu").css({'top': body_top});
        
		
		/* ALEXIS DIRTY HACK TO CORRECT MENU HOVER */
		$(this).parent('ul').children('li').each(function(){
			$(this).children('a').css({'color': '#000000'});
		});
		 
    });
    $(".block-superfish-1 ul#superfish-1 > li").mouseout(function() {
        $("#page").removeClass("menu-hover");
        $('#bloc-menu-hover').remove();
        $('#overlay_menu').remove();
		
		/* ALEXIS DIRTY HACK TO CORRECT MENU HOVER */
		$(this).parent('ul').children('li').each(function(){
			$(this).children('a').css({'color': '#ffffff'});
		});
		
    });
    
    $('#btn_up').click(function() {
        $('html,body').animate({scrollTop: 0}, 'slow');
        return false;
    });
    
    /**
     * ScrollTop 
     */
    $(window).scroll(function(){
        if($(window).scrollTop()<50) {
            $('#btn_up').fadeOut();
        }
        else {
            $('#btn_up').fadeIn();
        }
    });
  
    /**
     * Override selectBox 
     */
    var width_discipline_selectbox_a = 132;
    var height_selectbox = 31;
    var width_type_selectbox_a = 107;
    var width_discipline_selectbox_label = 113;
    var width_type_selectbox_label = 85;
    var width_lang_switch_selectbox_a = 193;
    var width_lang_switch_label = 177;
    var height_lang_switch_selectbox = 21;
    $('.block1-filter a.selectBox').width(width_discipline_selectbox_a);
    $('.block2-filter a.selectBox').width(width_type_selectbox_a);
    $('.block1-filter .selectBox-dropdown .selectBox-label').width(width_discipline_selectbox_label);
    $('.block2-filter .selectBox-dropdown .selectBox-label').width(width_type_selectbox_label);
    $('#filter-mavic .selectBox-dropdown').height(height_selectbox);
    
    $('.language-switcher a.selectBox').width(width_lang_switch_selectbox_a);
    $('.language-switcher .selectBox-dropdown .selectBox-label').width(width_lang_switch_label);
    $('.language-switcher .selectBox-dropdown').height(height_lang_switch_selectbox);
    
    /**
     * Desactivation selectbox in mobile display 
     */
    if(document.getElementsByClassName('responsive-layout-mobile')[0] != undefined) {
        var menu_zone_user1 = document.getElementById('superfish-2');
        var menu_zone_user1_select = document.getElementById('superfish-2-select');
        var menu_zone_user2_select = document.getElementById('superfish-3-select');
        menu_zone_user1.style.display = 'block'; 
        menu_zone_user1_select.style.display = 'none'; 
        menu_zone_user2_select.style.display = 'none'; 
    }
    
    /**
     * Detection device 
     */
    $.browser.device = (/android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase()));
    console.log($.browser);
   /* if($.browser.device == true) {
        var menu_zone_user1 = $('superfish-2');
        var menu_zone_user1_select = $('superfish-2-select');
        var menu_zone_user2_select = $('superfish-3-select');
        console.log(menu_zone_user1 + 'menu_zone_user1');
        console.log(menu_zone_user1_select + 'menu_zone_user1_select');
        console.log(menu_zone_user2_select + 'menu_zone_user2_select');
        if(menu_zone_user1 != 'undefined') menu_zone_user1.style.display = 'block'; 
        if(menu_zone_user1_select != 'undefined') menu_zone_user1_select.style.display = 'none'; 
        if(menu_zone_user2_select != 'undefined') menu_zone_user2_select.style.display = 'none';
        var button_up = $('btn_up');
        if(button_up != 'undefined') button_up.style.display = 'none !important';
        
    }*/
	   
	/* ALEXIS DIRTY HACK TO CORRECT MENU HOVER */
	   $('#superfish-1').mouseover(function(){
		   if($('#bloc-menu-hover').length == 0)
		  	{
		   	 	$(this).children('li').each(function(){
		   	 		$(this).children('a').css({'color': '#ffffff'});
		   	 	});
	   		}
	   });
	   
	   
	}   
});

function resizeZoneMenuWrapper()
{
   if(jQuery(window).width() <= 740)
   {
   		//jQuery('#region-menu ul#superfish-1').css({'width': jQuery('#region-menu ul#superfish-1').width()/2});
   		jQuery('#logo').attr({src: "/sites/all/themes/custom/mavic_theme/logo-small.png", alt: "logo mavic"});
   }
}