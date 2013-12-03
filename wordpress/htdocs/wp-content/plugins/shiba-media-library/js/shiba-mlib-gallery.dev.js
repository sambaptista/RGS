(function($){
gallerySort = {
//	var gallerySortable, gallerySortableInit, desc = false;

	// get_selected_media(document.posts-filter.list)
	init : function() { // gallerySortableInit
		gallerySortable = $('#the-list').sortable( {
			items: 'tr',
			placeholder: 'sorthelper',
			axis: 'y',
			distance: 2
		} );
	},

	hidden : function(name, value, f) {
		  $('<input>').attr({
			  type: 'hidden',
			  name: name,
			  id: name,
			  value: value
		  }).appendTo(f);
	},

	getMedia : function(gName, lName) {
		var l = $('#' + lName); // image list
		var g = $('#' + gName); // gallery object

		$('input[name="media[]"]', l).each(function() { 
			shibaMediaForm.hidden('media[]', $(this).val(), g);
		});
	}
};

$(document).ready(function(){
	// initialize sortable
	gallerySort.init();
	
	jQuery('#submitdiv #publish, #submitdiv #save-post').click(function(e) {
		gallerySort.getMedia('post', 'gallery-list');
	});
});


})(jQuery);