Cufon.replace('.helvetica');

$(window).bind("resize", function(){
	$("#body-background").ezBgResize();	
	checkSize();
});


function checkSize()
{
	$('#container').css("height",$(window).height()+"px");
	/* désactiver pour le moment
	if( $(window).width() < 1024 )
	{
		$('#logo').addClass("small_size");
		$('#right_content').addClass("small_size");		
	}
	else
	{
		$('#logo').removeClass("small_size");
		$('#right_content').removeClass("small_size");
	}*/
}

function createRightContentLink()
{
	$('#right_content .link').each(
		function()
		{
			$(this).mouseover(function()
			{
				$(this).addClass("colored");
			});
			$(this).mouseout(function()
			{
				$(this).removeClass("colored");
			});
			$(this).click(function()
				{
					link_to_go = $(this).attr('title');
					$(this).removeAttr('title');
					location.href = link_to_go;
				}
			);
		}
	);
}

function overItem(index)
{
	document.getElementById('compare'+index).style.visibility = 'visible';
	document.getElementById('altpop'+index).style.visibility = 'visible';
	document.getElementById('item'+index).className= "name active";	
}

function outItem(index)
{
	document.getElementById('compare'+index).style.visibility = 'hidden';
	document.getElementById('altpop'+index).style.visibility = 'hidden';
	document.getElementById('item'+index).className= "name";	
}

function overTab(elem)
{
	elem.className = elem.className + " tabover";
}

function outTab(elem)
{
	elem.className = elem.className.replace(new RegExp(' tabover','g'),'');
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
}


function display_submenu(id)
{
	removeActiveMenu();
	$('.submenu').each(function()
	{
		$(this).css('display','none');
	});
	$('#menu_'+id).addClass("active");
	$('#black_screen').css('height',$(document).height());
	$('#black_screen').css('width',$(document).width());
	$('#black_screen').css('visibility','visible');
	$('#black_screen').css('opacity','0.5');
	$('#black_screen').css('top','0');
	$('#black_screen').css('left','0');
	$('#black_screen').css('zIndex','8000');
	$('#menu').css('zIndex','10000');
	$('#submenu_'+id).css('display','block');
	if ($.browser.msie && $.browser.version.substr(0,1)<7)
	{
		$('#black_screen').bgiframe();
	}
	$('#black_screen').click(function()
	{
		$('#black_screen').css('visibility','hidden');
		$('#submenu_'+id).css('display','none');
		removeActiveMenu();
	});
}

function removeActiveMenu()
{
	$('#menu a').each(function()
	{
		$(this).removeClass('active');
	});
}

function show_hide_filters()
{  
	if($('#filters_content').css('display') == 'none')
	{
		$('#product_compare_block').css('display','none');
		$('#filters_button').addClass("active");
		$('#filters_content').slideDown();
		document.getElementById('filters_button_img').src = "images/filters_up.gif";
	}
	else
	{
		$('#filters_content').slideUp();
		$('#product_compare_block').css('display','block');
		$('#filters_button').removeClass("active");
		document.getElementById('filters_button_img').src = "images/filters.gif";
	}
}

function white_opacity()
{
	//inutile
	/*$('.white_opacity').each(

	function(){ $(this).css('opacity','0.5'); }
	);*/
}