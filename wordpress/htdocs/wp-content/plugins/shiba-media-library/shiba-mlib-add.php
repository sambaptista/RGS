<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Media_Library_Add")) :

class Shiba_Media_Library_Add {

	function Shiba_Media_Library_Add() {
		global $shiba_mlib;

		wp_register_script('shiba-mlib-form', SHIBA_MLIB_URL . '/js/shiba-mlib.dev.js', array(),'1.7', TRUE);
		wp_enqueue_script('shiba-mlib-form');

		// New Gallery Page
		add_meta_box(	'gallery-type-div', __('Gallery Type'), array(&$this, 'gallery_type_metabox'), 'gallery', 'normal', 'high');
		add_meta_box(	'post-content-div', __('Gallery Description'), array(&$this, 'gallery_description_metabox'), 'gallery', 'normal', 'high');
//		remove_meta_box('tagsdiv-post_tag','gallery','core');
		add_meta_box(	'tagsdiv-post_tag', __('Gallery Tags'), 
						array(&$shiba_mlib->tag_metabox,'post_tags_meta_box'), 'gallery', 'normal', 'high'); 
		add_action('save_post', array(&$this,'save_gallery_data') );

		// Add advanced form for new gallery page
//		wp_enqueue_script('admin-gallery');
		add_thickbox();
		wp_enqueue_script('shiba-mlib-gallery', SHIBA_MLIB_URL.'/js/shiba-mlib-gallery.dev.js', array( 'jquery-ui-sortable' ), '1.1', true);

		add_action('edit_form_advanced', array(&$this,'edit_gallery__advanced_form'));
		add_filter('post_updated_messages', array(&$this,'gallery_updated_messages'));

		// Add Quick Edit functionality
		require_once(SHIBA_MLIB_DIR.'/shiba-mlib-qedit.php');
		if (class_exists("Shiba_Media_Library_QEdit"))
			$shiba_mlib->qedit = new Shiba_Media_Library_QEdit();	

	}
	
	function gallery_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['gallery'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Gallery updated. <a href="%s">View gallery</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Gallery updated.'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Gallery restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Gallery published. <a href="%s">View gallery</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Gallery saved.'),
			8 => sprintf( __('Gallery submitted. <a target="_blank" href="%s">Preview gallery</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Gallery scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview gallery</a>'),
			  // translators: Publish box date format, see http://php.net/date
			  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Gallery draft updated. <a target="_blank" href="%s">Preview gallery</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			11 => __('Media removed from gallery.'),
			12 => __('Media permanently deleted.'),
			13 => __('Media moved to the trash.'.' <a href="' . esc_url( wp_nonce_url( 'upload.php?doaction=undo&action=untrash&ids='.(isset($_GET['ids']) ? $_GET['ids'] : ''), "bulk-media" ) ) . '">' . __('Undo') . '</a>'),
			14 => __('Media restored from the trash.')
		  );

		return $messages;
	}
	


	function edit_gallery__advanced_form() {
		global $shiba_mlib, $is_trash, $wp_query, $menu_order;
		
		if (!isset($_GET['post'])) return;
		$id = absint($_GET['post']);
//		$gallery = get_post($id);
//		$action = 'updategallery';
		$post_type = get_post_meta($id, '_gallery_type', TRUE);
		$menu_order = get_post_meta($id, '_menu_order', TRUE);
		if (!$post_type) $post_type = 'attachment';
		// Can't paginate if we are adding in tagged attachment results    
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'post_status' => 'any',
			'post_parent' => $id
			); 
		if (isset($_GET['orderby']))
			$args['orderby'] = esc_attr($_GET['orderby']);		
		if (isset($_GET['order']))
			$args['order'] = esc_attr($_GET['order']);		
		query_posts($args);
						
//		if (!isset($_GET['post_type']) || ($_GET['post_type'] != 'gallery')) return;

		// list images in gallery
		$image_title = __('Add an Image');    
		// add upload.php in referer address
		$_SERVER['REQUEST_URI'] = add_query_arg( array('redirect_back' => 'upload.php'), $_SERVER['REQUEST_URI']);
		?>
        </div> <!-- This ends post-body-content -->
       	</div> <!-- This ends post-body -->
      	</div> <!-- This ends post-stuff -->
      	</form> <!-- This ends post form -->
		<div style="clear:both;"></div>
		
		<h3 style="float:left;padding-right:20px;">Gallery Images</h3>
		
		<div style="position:relative; top:10px;">  

		<?php
			$image_library_url = get_upload_iframe_src( 'image', null, 'library' );
			$image_library_url = remove_query_arg( array('post_id', 'TB_iframe'), $image_library_url );
			$image_library_url = add_query_arg( array( 'post_id' => $id, 'context' => 'shiba-add-new-to-gallery', 'TB_iframe' => 1 ), $image_library_url );
		?>

		<a id="shiba-add-new-to-gallery" class="button thickbox" href="<?php echo esc_url( $image_library_url ); ?>"><?php _e( 'Add New' ); ?></a>
		</div>
	 
		<form id="gallery-list" action="upload.php" method="<?php if (class_exists('Shiba_Media_List_Table')) echo 'post'; else echo 'get';?>">
			<?php wp_nonce_field('bulk-media'); ?>			
			<div class="tablenav">
		   
			 <div class="alignleft actions">
			<select name="action" id="mlib_action" class="select-action">
			<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
			<option value="remove"><?php _e('Detach from Gallery'); ?></option>
			<?php if (function_exists('wp_trash_post') && !$is_trash) { ?>
			<option value="trash"><?php _e('Move to Trash'); ?></option>
			<?php } ?>       
<!--		Removed "Delete Permanently" option because it ONLY works on attachments and causes errors for others	
			<option value="delete"><?php _e('Delete Permanently'); ?></option> 
-->           
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="mlib_doaction" class="button-secondary action" onClick="shibaMediaForm.addActions(jQuery('#gallery-list'));"/>
			</div> <!-- End alignleft actions -->
			
			<div style="clear:both;"></div>
			</div> <!-- End tablenav -->
			
			<style>
				#the-list td.column-parent a.hide-if-no-js { display:none; }
			</style>
	
			<?php
			global $post;
			if (class_exists('Shiba_Media_List_Table') && isset($shiba_mlib->media_table)) {
				// for WordPress 3.1 and above	
				$shiba_mlib->media_table->display_media_table();	
			}	
			?>       	
			<div style="clear:both;"></div>
		</form> <!-- End gallery-list form -->
        
        <?php
        // Render Quick Edit Form 
		if (class_exists('Shiba_Media_List_Table') && isset($shiba_mlib->media_table)) {
			$columns = $shiba_mlib->media_table->get_column_info(); 
			$col_count = count($columns[0]) - count($columns[1]);
			$shiba_mlib->qedit->render_inline_edit_form(0, $col_count);
		}
		?>

        <form> <!-- Need to open up div to match the close post form -->
        <div> <!-- Need to open up div to match the close div of post-stuff -->
        <div> <!-- Need to open up div to match the close div of post-body -->       
        <div> <!-- Need to open up div to match the close div of post-body-content -->

		<?php
	}



	function gallery_description_metabox($post) {
		?>
		<label class="screen-reader-text" for="excerpt"><?php _e('Description') ?></label>
        <textarea rows="5" cols="40" name="content" tabindex="6" id="content"><?php echo $post->post_content; ?></textarea>
		<p><?php _e('The description is not prominent by default, however some plugins may show it.'); ?></p>
		<?php
	}
	
	function gallery_type_metabox($post) {
		$gallery_type = get_post_meta($post->ID, '_gallery_type', TRUE);
		if (!$gallery_type) $gallery_type = 'attachment'; 	 
		?>
        <style>
		#gallery-type-div { margin-top: 80px; }
		</style>
        <input type="hidden" name="gallery_type_noncename" id="gallery_type_noncename" value="<?php echo wp_create_nonce( 'gallery_type'.$post->ID );?>" />
		<input type="radio" name="gallery_type" value="any" <?php if ($gallery_type == 'any') echo "checked=1";?>> Any.<br/>
		<input type="radio" name="gallery_type" value="attachment" <?php if ($gallery_type == 'attachment') echo "checked=1";?>> Only Attachments.<br/>
		<input type="radio" name="gallery_type" value="post" <?php if ($gallery_type == 'post') echo "checked=1";?>> Only Posts.<br/>
		<input type="radio" name="gallery_type" value="gallery" <?php if ($gallery_type == 'gallery') echo "checked=1";?>> Only Galleries.<br/>
		<?php
	}
	
	function update_media_menu_order($media_items) {
		global $wpdb;
		$i = 1;
		foreach ($media_items as $item) {
			$query = $wpdb->prepare("UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d", $i, $item);
			$wpdb->query($query);
			$i++;	
		}
	}
	
	function save_gallery_data($post_id) {	
		// verify this came from the our screen and with proper authorization.
		if ( !isset($_POST['gallery_type_noncename']) || !wp_verify_nonce( $_POST['gallery_type_noncename'], 'gallery_type'.$post_id )) {
			return $post_id;
		}
	
		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
	
		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		
		// OK, we're authenticated: we need to find and save the data	
		$post = get_post($post_id);
		if ($post->post_type == 'gallery') { 
			update_post_meta($post_id, '_gallery_type', esc_attr($_POST['gallery_type']) );
			if (isset($_POST['media']) && is_array($_POST['media'])) {
				$this->update_media_menu_order($_POST['media']);
				update_post_meta($post_id, '_menu_order', array_flip($_POST['media']) );
			}	
		}
		// Set post category to default if necessary
		$post_category = $_POST['post_category'];
		if (is_array($post_category) && isset($post_category[0]) && !$post_category[0])
			unset($post_category[0]);
		if ( empty($post_category) || 0 == count($post_category) || !is_array($post_category) ) 
			if ( 'gallery' == $_POST['post_type'] && 'auto-draft' != $_POST['post_status'] ) {
				wp_set_post_categories($post_id, array( get_option('default_category') ));
			}	
		return $post_id;
	}	
} // end Shiba_Media_Library_Add class
endif;
?>