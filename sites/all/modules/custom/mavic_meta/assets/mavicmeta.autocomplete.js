/**
 * Based on drupal autocomplet.js : enhanced to work on a textarea instead of a textfield
 */
var tokenList = new Array();
Drupal.mavicmetautocomplete = Drupal.mavicmetautocomplete || {};
/**
 * Attaches the autocomplete behavior to all required fields
 */
Drupal.behaviors.mavicmetautocomplete = {
    attach: function (context) {
    	var acdb = [];
    	$('textarea', context).each(function () {//:not(.autocomplete-processed)
    		var uri = this.id;
    		if (!acdb[uri]) {
    			acdb[uri] = new Drupal.mavicmetACDB(uri);
    		}
    		var input = $('#' + this.id).attr('autocomplete', 'OFF')[0];
    
    		//$(input.form).submit(Drupal.mavicmetautocompleteSubmit);
    		new Drupal.mavicmetjsAC(input, acdb[uri]);
    		$(this).addClass('autocomplete-processed');
    	});
    };
};

/**
 * Prevents the form from submitting if the suggestions popup is open
 * and closes the suggestions popup when doing so.
 */
Drupal.mavicmetautocompleteSubmit = function () {
	return $('#autocomplete').each(function () {
		this.owner.hidePopup();
	}).size() == 0;
};

/**
 * An AutoComplete object
 */
Drupal.mavicmetjsAC = function (input, db) {
	var ac = this;
	this.input = input;
	this.db = db;

	$(this.input)
	.keydown(function (event) {
		return ac.onkeydown(this, event);
	})
	.keyup(function (event) {
		ac.onkeyup(this, event);
	})
	.blur(function () {
		ac.hidePopup();
		ac.db.cancel();
	});

};


/**
 * Handler for the "keydown" event
 */
Drupal.mavicmetjsAC.prototype.onkeydown = function (input, e) {
	if (!e) {
		e = window.event;
	}
	switch (e.keyCode) {
		case 40: // down arrow
			this.selectDown();
			return false;
		case 38: // up arrow
			this.selectUp();
			return false;
		default: // all other keys
			return true;
	}
};

/**
 * Handler for the "keyup" event
 */
Drupal.mavicmetjsAC.prototype.onkeyup = function (input, e) {
	if (!e) {
		e = window.event;
	}
	switch (e.keyCode) {
		case 16: // shift
		case 17: // ctrl
		case 18: // alt
		case 20: // caps lock
		case 33: // page up
		case 34: // page down
		case 35: // end
		case 36: // home
		case 37: // left arrow
		case 38: // up arrow
		case 39: // right arrow
		case 40: // down arrow
			return true;

		case 9:  // tab
		case 13: // enter
		case 27: // esc
			this.hidePopup(e.keyCode);
			return true;

		default: // all other keys
			if (input.value.length > 0)
				this.populatePopup();
			else
				this.hidePopup(e.keyCode);
			return true;
	}
};

/**
 * Puts the currently highlighted suggestion into the autocomplete field
 */
Drupal.mavicmetjsAC.prototype.select = function (node) {
	this.input.value = node.autocompleteValue;
};

/**
 * Highlights the next suggestion
 */
Drupal.mavicmetjsAC.prototype.selectDown = function () {
	if (this.selected && this.selected.nextSibling) {
		this.highlight(this.selected.nextSibling);
	}
	else {
		var lis = $('li', this.popup);
		if (lis.size() > 0) {
			this.highlight(lis.get(0));
		}
	}
};

/**
 * Highlights the previous suggestion
 */
Drupal.mavicmetjsAC.prototype.selectUp = function () {
	if (this.selected && this.selected.previousSibling) {
		this.highlight(this.selected.previousSibling);
	}
};

/**
 * Highlights a suggestion
 */
Drupal.mavicmetjsAC.prototype.highlight = function (node) {
	if (this.selected) {
		$(this.selected).removeClass('selected');
	}
	$(node).addClass('selected');
	this.selected = node;
};

/**
 * Unhighlights a suggestion
 */
Drupal.mavicmetjsAC.prototype.unhighlight = function (node) {
	$(node).removeClass('selected');
	this.selected = false;
};

/**
 * Hides the autocomplete suggestions
 */
Drupal.mavicmetjsAC.prototype.hidePopup = function (keycode) {
	// Select item if the right key or mousebutton was pressed
	if (this.selected && ((keycode && keycode != 46 && keycode != 8 && keycode != 27) || !keycode)) {
		this.input.value = this.selected.autocompleteValue;
	}
	// Hide popup
	var popup = this.popup;
	if (popup) {
		this.popup = null;
		$(popup).fadeOut('fast', function() {
			$(popup).remove();
		});
	}
	this.selected = false;
};

/**
 * Positions the suggestions popup and starts a search
 */
Drupal.mavicmetjsAC.prototype.populatePopup = function () {
	// Show popup
	if (this.popup) {
		$(this.popup).remove();
	}
	this.selected = false;
	this.popup = document.createElement('div');
	this.popup.id = 'autocomplete';
	this.popup.owner = this;
	$(this.popup).css({
		marginTop: this.input.offsetHeight +'px',
		width: (this.input.offsetWidth - 4) +'px',
		display: 'none'
	});
	$(this.input).before(this.popup);

	// Do search
	this.db.owner = this;
	//this.db.search(this.input.value);
	this.db.search(this.db.ReturnWord(this.input.value, this.db.getCaret()), this.db.getCaret());
};

/**
 * Fills the suggestion popup with any matches received
 */
Drupal.mavicmetjsAC.prototype.found = function (matches,searchString,caretPos) {
	// If no value in the textfield, do not show the popup.
	if (!this.input.value.length) {
		return false;
	}

	// Prepare matches
	var ul = document.createElement('ul');
	var ac = this;
	for (key in matches) {
		var li = document.createElement('li');
		$(li)
		.html('<div>'+ matches[key] +'</div>')
		.mousedown(function () {
			ac.select(this);
		})
		.mouseover(function () {
			ac.highlight(this);
		})
		.mouseout(function () {
			ac.unhighlight(this);
		});
		var kresp = this.input.value.substr(0, caretPos - searchString.length) + key + this.input.value.substr(caretPos);
		li.autocompleteValue = kresp;
		$(ul).append(li);
	}

	// Show popup with matches, if any
	if (this.popup) {
		if (ul.childNodes.length > 0) {
			$(this.popup).empty().append(ul).show();
		}
		else {
			$(this.popup).css({
				visibility: 'hidden'
			});
			this.hidePopup();
		}
	}
};

Drupal.mavicmetjsAC.prototype.setStatus = function (status) {
	switch (status) {
		case 'begin':
			$(this.input).addClass('throbbing');
			break;
		case 'cancel':
		case 'error':
		case 'found':
			$(this.input).removeClass('throbbing');
			break;
	}
};

/**
 * An AutoComplete DataBase object
 */
Drupal.mavicmetACDB = function (uri) {
	this.uri = parseInt(uri.substr(5, uri.length - 5));
	this.delay = 300;
	this.tokens = tokenList["autocomplete_" + this.uri];



};

/**
 * Performs search
 */
Drupal.mavicmetACDB.prototype.search = function (searchString, caretPos) {
	var db = this;
	this.searchString = searchString;

	// Initiate delayed search
	if (this.timer) {
		clearTimeout(this.timer);
	}
	this.timer = setTimeout(function() {
		db.owner.setStatus('begin');
		if (searchString.indexOf("[")>=0) {


			// Verify if these are still the matches the user wants to see
			if (db.searchString == searchString) {
				db.owner.found(db.tokens,searchString,caretPos);
			}
			db.owner.setStatus('found');
		}  
	}, this.delay);
};
/**
 * Get caret current position
 */
Drupal.mavicmetACDB.prototype.getCaret = function () {
	var db = this;
	if (db.owner.input.selectionStart) {
		return db.owner.input.selectionStart;
	} else if (document.selection) {
		this.focus();

		var r = document.selection.createRange();
		if (r == null) {
			return 0;
		}

		var re = db.owner.input.createTextRange(),
		rc = re.duplicate();
		re.moveToBookmark(r.getBookmark());
		rc.setEndPoint('EndToStart', re);

		return rc.text.length;
	} 
	return 0;
}

/**
 * Get the word being typed
 */
Drupal.mavicmetACDB.prototype.ReturnWord = function(text, caretPos) {

	var index = text.indexOf(caretPos);
	var preText = text.substring(0, caretPos);
	if (preText.indexOf(" ") > 0) {
		var words = preText.split(" ");
		return words[words.length - 1]; //return last word
	}
	else {
		return preText;
	}
}


/**
 * Cancels the current autocomplete request
 */
Drupal.mavicmetACDB.prototype.cancel = function() {
	if (this.owner) this.owner.setStatus('cancel');
	if (this.timer) clearTimeout(this.timer);
	this.searchString = '';
};
