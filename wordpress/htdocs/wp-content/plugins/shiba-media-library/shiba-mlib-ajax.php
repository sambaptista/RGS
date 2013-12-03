<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

/**
 * Functions that support AJAX callbacks.
 *
 */
if (!class_exists("Shiba_Media_Ajax")) :
	 
class Shiba_Media_Ajax {

	function Shiba_Media_Ajax() {
		global $shiba_mlib;

		// AJAX support functions
		// Add attachment tags to tag box
		add_filter('sanitize_title', array(&$this,'tag_box_title')); // for below 3.1  
		add_filter('sanitize_key', array(&$this,'tag_box_title')); // for 3.1  

		// AJAX for Quick Edit button
		add_action('wp_ajax_shiba_media_quick_edit', array(&$this,'wp_ajax_update_media'));
	}
	
	function wp_ajax_update_media() {
		global $wp_query, $shiba_mlib;

		check_ajax_referer( 'update-media', '_ajax_nonce-update-media' );
		
		$attachment_id = (int) $_POST['attachment_id'];

		if ( !current_user_can('edit_post', $attachment_id) )
			wp_die ( __('You are not allowed to edit this attachment.') );

		set_current_screen( esc_attr($_POST['screen']) );
		// Restructure data for media upload form handler
		foreach ($_POST as $key => $value) {
			if ( in_array($key, array('attachment_id', 'action', '_ajax_nonce-update-media','_wpnonce',  '_wp_http_referer')) )
				continue;
			$_POST['attachments'][$attachment_id][$key] = $value;
		}
		$errors = media_upload_form_handler();

		if (!class_exists("Shiba_Media_List_Table")) {
			require_once(SHIBA_MLIB_DIR.'/shiba-media-table.php');
			$shiba_mlib->media_table = new Shiba_Media_List_Table();	
		}
		
		if (isset($shiba_mlib->media_table)) {
		  query_posts( "p={$attachment_id}" );
	  
		  ob_start();
  //			$wp_list_table->single_row( $attachment );
			  $shiba_mlib->media_table->display_rows();
			  $attachment_list_item = ob_get_contents();
		  ob_end_clean();
		}
		$x = new WP_Ajax_Response();
	
		$x->add( array(
			'what' => 'attachment',
			'id' => $attachment_id,
			'data' => $attachment_list_item
		));
	
		$x->send();
		
	}
	
	// For post tag metabox
	function tag_box_title($title) {
		switch ($title) {
		case 'post_tag-shiba_post':
			$this->generate_tag_cloud('post');
			break;
		case 'post_tag-shiba_attachment':
			$this->generate_tag_cloud('attachment');
			break;
		case 'post_tag-shiba_gallery':
			$this->generate_tag_cloud('gallery');
			break;
		}
		return $title;
	}


	function generate_tag_cloud($post_type) {
		global $wpdb;

		// database calls must be sensitive to multisite
		$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", $post_type);
		$attachment_ids = $wpdb->get_col($query);
		$terms = wp_get_object_terms($attachment_ids, 'post_tag', array('orderby' => 'count', 'order' => 'DESC'));
		$tags = array(); 
		// limit to 45 tags
		foreach ($terms as $term) {
			$tags[$term->term_id] = $term;	
			if (count($tags) >= 45) break;	
		}
	
		if ( empty( $tags ) )
			die( __('No tags found!') );
	
		if ( is_wp_error($tags) )
			die($tags->get_error_message());
	
		foreach ( $tags as $key => $tag ) {
			$tags[ $key ]->link = '#';
			$tags[ $key ]->id = $tag->term_id;
		}
	
		// We need raw tag names here, so don't filter the output
		$return = wp_generate_tag_cloud( $tags, array('filter' => 0) );
	
		if ( empty($return) )
			die('0');
	
		echo $return;
	
		exit;
	}

} // end class	
endif;
?>