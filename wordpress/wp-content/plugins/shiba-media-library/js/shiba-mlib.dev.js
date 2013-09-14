(function($){
shibaMediaForm = {
	// get_selected_media(document.posts-filter.list)

	hidden : function(name, value, f) {
		  $('<input>').attr({
			  type: 'hidden',
			  name: name,
			  value: value
		  }).appendTo(f);
	},
	
	addActions : function(f) { // addMediaActions
		// If action is remove then unset doaction so that it does not get caught in 
		// upload.php
		var action = $('#mlib_action').val(); 
		if ((action == 'remove') || (action == 'set_tags') || (action == 'add_tags')) {
			
			jQuery('input[name=_wp_http_referer]', f).remove();
//			$('input[name=_wp_http_referer]', f).each(function() { alert($(this).val()); });
			$('#mlib_doaction').attr('name', 'shiba_doaction');
			// For 3.1 need to rename action and action2
			$('#mlib_action').attr('name', 'shiba_action');
		}
	},	
	
	redirect : function (f) { // redirectPage
		// Redirect to previous page by adding previous mime_type and detached state
		var hasType = location.href.indexOf('post_mime_type');
		if (hasType >= 0) {
			var sPos = location.href.indexOf('=', mimeType);
			var ePos = location.href.indexOf('&', sPos);
			
			if (ePos >= 0) {
				var mimeStr = location.href.substring(sPos+1, ePos);
			} else {
				var mimeStr = location.href.substring(sPos+1);
			}
			shibaMediaForm.hidden('post_mime_type', mimeStr, f);												
		}
		
		if (location.href.indexOf('detached') >= 0) {
			shibaMediaForm.hidden('detached', '1', f);												
		}
	},

	getMedia : function(form) { //  processMediaPlusForm
		var s = $('#' + form);
		var t = $('#shiba-mlib-form');
		// get all checked input elements
		$('input[name="media[]"]:checked', s).each(function() { 
			shibaMediaForm.hidden('media[]', $(this).val(), t);
		});
		
		// prepare form actions so that it will be properly processed
		shibaMediaForm.addActions(t);
		shibaMediaForm.redirect(t);
	}
};

})(jQuery);
