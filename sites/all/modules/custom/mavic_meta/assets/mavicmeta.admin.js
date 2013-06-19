
Drupal.mavicmetadmin = Drupal.mavicmetadmin || {};

var imgRemoval = new Array();

/**
 * drupal behaviors proto
 */
Drupal.behaviors.mavicmetadmin = {
    attach: function (context) {
    	$('#edit-langOpt').change(function () {
    		var lang = $(this).val(), throbber = $('<div class="ahah-progress ahah-progress-throbber"><div class="throbber">&nbsp;</div></div>');
    		$(this).addClass('progress-disabled').attr('disabled', true).after(throbber);
    		window.location = "http://" + window.location.hostname + "/" + lang + "/mavicmeta/settings";
    	});
	};
};

