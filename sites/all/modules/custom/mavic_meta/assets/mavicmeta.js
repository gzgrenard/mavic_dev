
Drupal.mavicmeta = Drupal.mavicmeta || {};

var imgRemoval = new Array();

/**
 * drupal behaviors proto
 */
/*
Drupal.behaviors.mavicmeta = function (context) {
	$('.imgToHide').each(function(i){
		imgRemoval[i] = $(this).remove();
	});

	$('.remove_img').click(function(e){
		e.preventDefault();
		imgRemoval[$('.remove_img').index(e.currentTarget)].appendTo($(this).parent());
		$(this).siblings('img').remove();
		$(this).remove();
	});

	$('[data-tooltip]').each(function(){
		$(this).data('tooltip', $(this).attr('data-tooltip')).hover(Drupal.mavicmeta.tooltipOver, Drupal.mavicmeta.tooltipOut);
	});

};
*/
Drupal.behaviors.mavicmeta = {
    attach: function (context) {
        $('.imgToHide').each(function(i){
            imgRemoval[i] = $(this).remove();
        });
        
        $('.remove_img').click(function(e){
            e.preventDefault();
            imgRemoval[$('.remove_img').index(e.currentTarget)].appendTo($(this).parent());
            $(this).siblings('img').remove();
            $(this).remove();
        });
        
        $('[data-tooltip]').each(function(){
            $(this).data('tooltip', $(this).attr('data-tooltip')).hover(Drupal.mavicmeta.tooltipOver, Drupal.mavicmeta.tooltipOut);
        });
    };
};

/**
* Handler for redirecting
*/

Drupal.mavicmeta.redirect = function (lang, cat, menuItem) {
	var mI = '', wait = $('<p></p>');
	wait.insertBefore('#edit-mavicmeta-lang-wrapper').css({'width':'100%','text-align':'center','font-weight':'bold','float':'left','background':'url("/images/loader.gif") no-repeat'}).html('Wait while loading...');
	if (menuItem != undefined) {
		var mI = "/" + menuItem;
	}
	window.location = "http://" + window.location.hostname + "/" + lang + "/mavicmeta/list/" + lang + "/" + cat + mI;
}

/**
* Handler for tooltip
*/

Drupal.mavicmeta.tooltipOver = function () { 
	var xy = $(this).offset();
	var test = $(this).data("tooltip");
	var tooltipDisplayer = ($('#tooltipDisplayer').length) ? $('#tooltipDisplayer') : $('<div></div>').attr('id', 'tooltipDisplayer').appendTo('body').hide();
	tooltipDisplayer.html($(this).data('tooltip')).css({top: xy.top + 10 + "px", left: xy.left + 30 + "px"}).fadeIn(500);
}
Drupal.mavicmeta.tooltipOut = function () {
	$('#tooltipDisplayer').fadeOut(300);
}

/**
 * Handler for populating fields with token's values
 */
Drupal.mavicmeta.getTokens = function (tokens, target) {
	if ($('#edit-' + target + '-tokens').attr("checked")){
		for (var key in tokens) {
			$('#' + key).data('tokenised', $('#' + key).val());
			$('#' + key).val(tokens[key]).attr('disabled','disabled');
		}
	} else {
		for (var key in tokens) {
			$('#' + key).removeAttr('disabled').val($('#' + key).data('tokenised'));
		}
	}
}


$(function(){
	//override ahah success to fire a custom javascript function, if it's in the response object
	Drupal.ahah.prototype.success = function (response, status){
		if(!!response.js){ //fire custom javascript callback 
			if (response.js['func'] === "Drupal.mavicmeta.redirect" || response.js['func'] === "Drupal.mavicmeta.getTokens") {
				eval(response.js['func']+'('+(!!response.js['params'] ? response.js['params'] : '')+');');
				if(this.progress.element){$(this.progress.element).remove();}
		if(this.progress.object){this.progress.object.stopMonitoring();}
		$(this.element).removeClass('progress-disabled').attr('disabled', false)
				return true;
			}
		}

		//customCallback();
		var wrapper = $(this.wrapper);
		var form = $(this.element).parents('form');
		var new_content = $('<div></div>').html(response.data);
		form.attr('action', this.form_action);
		this.form_target ? form.attr('target', this.form_target) : form.removeAttr('target');
		this.form_encattr ? form.attr('target', this.form_encattr) : form.removeAttr('encattr');
		if(this.progress.element){$(this.progress.element).remove();}
		if(this.progress.object){this.progress.object.stopMonitoring();}
		$(this.element).removeClass('progress-disabled').attr('disabled', false);
		Drupal.freezeHeight();
		if(this.method == 'replace'){wrapper.empty().append(new_content);}
		else{wrapper[this.method](new_content);}
		if(this.showEffect!='show'){new_content.hide();}
		if(($.browser.safari && $("tr.ahah-new-content", new_content).size()>0)){new_content.show();}
		else if($('.ahah-new-content', new_content).size()>0){
			$('.ahah-new-content', new_content).hide();
			new_content.show();
			$(".ahah-new-content", new_content)[this.showEffect](this.showSpeed);
		}
		else if(this.showEffect != 'show'){new_content[this.showEffect](this.showSpeed);}
		if(new_content.parents('html').length > 0){Drupal.attachBehaviors(new_content);}
		Drupal.unfreezeHeight();
	};
});
