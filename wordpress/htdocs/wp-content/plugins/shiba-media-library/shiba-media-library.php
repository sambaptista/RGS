<?php
/*
Plugin Name: Shiba Media Library
Plugin URI: http://shibashake.com/wordpress-theme/media-library-plus-plugin
Description: This plugin enhances the existing WordPress Media Library; allowing you to easily attach and reattach images as well as link an image to multiple galleries by using tags.
Version: 3.4.7
Author: ShibaShake
Author URI: http://shibashake.com
*/


/*  Copyright 2009  ShibaShake 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


define( 'SHIBA_MLIB_DIR', WP_PLUGIN_DIR . '/shiba-media-library' );
define( 'SHIBA_MLIB_URL', WP_PLUGIN_URL . '/shiba-media-library' );


if (!class_exists("Shiba_Media_Library")) :


class Shiba_Media_Library {
	var $add, $manage, $upload, $tag_metabox, $ajax, $helper, $qedit, $media_table;
	var $options, $options_page;
	var $query_args='';
	var $permalink_obj;
	var $debug;
	
	function Shiba_Media_Library() {	
		global $wp_rewrite;
		$version = get_bloginfo('version');
		$this->debug = FALSE;

		require('shiba-mlib-permalink.php');
		if (class_exists("Shiba_Media_Permalink")) {
			$this->permalink_obj = new Shiba_Media_Permalink();	
		}
		
		if (is_admin()) {
			require(SHIBA_MLIB_DIR . '/shiba-mlib-helper.php');
			if (class_exists("Shiba_Media_Library_Helper")) {
				$this->helper = new Shiba_Media_Library_Helper();	
				add_action('admin_menu', array(&$this->helper,'add_pages') );
			}		
		}	

		$this->options = get_option('shiba_mlib_options');
		if (!is_array($this->options)) $this->options = $this->init_options();

		add_action('admin_init', array(&$this,'admin_init') );
		add_action('init', array(&$this,'init_general') );
		add_action('admin_menu', array(&$this,'add_pages') );
		register_activation_hook( __FILE__, array(&$this,'activate' ) );
		register_deactivation_hook( __FILE__, array(&$this,'deactivate' ) );
	}
	
	function activate($networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_activate($networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_activate($networkwide);		
	}

	function deactivate($networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_deactivate($networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_deactivate($networkwide);		
	}


	function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;
		if (is_plugin_active_for_network('shiba-media-lirbary/shiba-media-library.php')) {
			$old_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
			$this->_activate(TRUE);
			switch_to_blog($old_blog);
		}
	}
		
	function _activate($networkwide) {
		$this->init_options();
		// Activate gallery permalink
		$this->permalink_obj->activate();
				
    	global $wp_rewrite;
		if ($networkwide) {
			$this->permalink_obj->init();
			$gallery_structure = get_option('gallery_structure');			
			$this->permalink_obj->add_rewrite_rules($gallery_structure, EP_NONE);
		} else {
			$this->permalink_obj->init();			
			$wp_rewrite->flush_rules();	
		}	
	}

	function _deactivate($networkwide) {
    	global $wp_rewrite;		
		if ($networkwide) {
			$this->permalink_obj->init();
			$gallery_structure = get_option('gallery_structure');			
			$this->permalink_obj->remove_rewrite_rules($gallery_structure, EP_NONE);
		} else {
			$wp_rewrite->add_permastruct( 'gallery', '');
			$wp_rewrite->flush_rules();	
		}	
	}
	
	function init_options() {
		// Make sure all required option values are filled - with defaults if necessary
		static $default_options = array(	'shortcode' => '[gallery]' );
		$options = get_option('shiba_mlib_options');
		if (!is_array($options)) $options = array();

		$options = array_merge($default_options, $options);
		update_option('shiba_mlib_options', $options);		
		return $options;
	}

	function admin_init() {

		require(SHIBA_MLIB_DIR.'/shiba-tag-metabox.php');
		if (class_exists("Shiba_Tag_Metabox"))
			$this->tag_metabox = new Shiba_Tag_Metabox();	

		require(SHIBA_MLIB_DIR.'/shiba-mlib-ajax.php');
		if (class_exists("Shiba_Media_Ajax"))
			$this->ajax = new Shiba_Media_Ajax();	
		
		$this->helper->admin_init();
		$this->permalink_obj->admin_init();
			
		// Filters for all admin pages
		add_filter('attachment_fields_to_edit', array(&$this,'attachment_fields_to_edit'), 10, 2);
		add_filter('attachment_fields_to_save', array(&$this,'attachment_fields_to_save'), 10, 2);
	}

	
	function add_pages() {

		if ($this->check_upload_image_context('shiba-add-new-to-gallery')) {
			// Remove insert into post and use as featured image buttons
			add_filter('attachment_fields_to_edit', array(&$this, 'add_new_image_link'), 20, 2);
			add_filter('media_upload_tabs', array(&$this,'add_new_image_tabs'), 10, 1);
			add_filter( 'media_upload_mime_type_links', '__return_empty_array' );
			// Hide gallery settings
			add_action( 'admin_head', array(&$this, 'hide_gallery_settings') );
			// Don't show align, image size, and link 
			add_filter('attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2);

		}
	
	}


	function hide_gallery_settings(){ ?>
		 <style>
		 #gallery-settings { display:none !important; }
		 </style>
	<?php }

	function check_upload_image_context($context) {
		if (isset($_REQUEST['context']) && $_REQUEST['context'] == $context) {
			$this->new_images_loaded();
			return TRUE;
		} elseif (isset($_POST['attachments']) && is_array($_POST['attachments'])) { 
			// check for context in attachment objects 
 			$image_data = current($_POST['attachments']);
			if (isset($image_data['context']) && $image_data['context'] == $context ) {
				$this->new_images_loaded();
				return TRUE;
			}
		} elseif (isset($_POST['attachment_id']) && ($id = intval($_POST['attachment_id'])) && isset($_POST['fetch'])) {
			if ( !current_user_can('upload_files') ) return FALSE;
 			// New async upload - Array ( [attachment_id] => 9305 [fetch] => 1 ) 
			// Check the HTTP_REFERER for context
			if (strpos($_SERVER['HTTP_REFERER'],'context='.$context) !== FALSE)
				return TRUE;
		}
		return FALSE;
	}

	function new_images_loaded() {
		if (isset($_POST['save']) && $_POST['save'] == 'Save all changes' ) {
		  ?>
		  <script type="text/javascript">
		  /* <![CDATA[ */
		  var win = window.dialogArguments || opener || parent || top;
				  
		  //		win.tb_remove();
		  // submit the form
		  win.jQuery( '#post' ).submit();
		  /* ]]> */
		  </script>
		  <?php
		}
	}
	
	function add_new_image_tabs($_default_tabs) {
		unset($_default_tabs['type_url']);
		// don't show gallery because it doesn't make sense for post type objects 
		// and ordering does not work properly
		unset($_default_tabs['gallery']); 
		unset($_default_tabs['library']);
		
		return($_default_tabs);	
	}

	function add_new_image_link($form_fields, $post) {

		$attachment_id = $post->ID; $filename = basename( $post->guid );
		if ( current_user_can( 'delete_post', $attachment_id ) ) {
			if ( !EMPTY_TRASH_DAYS ) {
				$delete = "<a href='" . wp_nonce_url( "post.php?action=delete&amp;post=$attachment_id", 'delete-attachment_' . $attachment_id ) . "' id='del[$attachment_id]' class='delete'>" . __( 'Delete Permanently' ) . '</a>';
			} elseif ( !MEDIA_TRASH ) {
				$delete = "<a href='#' class='del-link' onclick=\"document.getElementById('del_attachment_$attachment_id').style.display='block';return false;\">" . __( 'Delete' ) . "</a>
				 <div id='del_attachment_$attachment_id' class='del-attachment' style='display:none;'>" . sprintf( __( 'You are about to delete <strong>%s</strong>.' ), $filename ) . "
				 <a href='" . wp_nonce_url( "post.php?action=delete&amp;post=$attachment_id", 'delete-attachment_' . $attachment_id ) . "' id='del[$attachment_id]' class='button'>" . __( 'Continue' ) . "</a>
				 <a href='#' class='button' onclick=\"this.parentNode.style.display='none';return false;\">" . __( 'Cancel' ) . "</a>
				 </div>";
			} else {
				$delete = "<a href='" . wp_nonce_url( "post.php?action=trash&amp;post=$attachment_id", 'trash-attachment_' . $attachment_id ) . "' id='del[$attachment_id]' class='delete'>" . __( 'Move to Trash' ) . "</a>
				<a href='" . wp_nonce_url( "post.php?action=untrash&amp;post=$attachment_id", 'untrash-attachment_' . $attachment_id ) . "' id='undo[$attachment_id]' class='undo hidden'>" . __( 'Undo' ) . "</a>";
			}
		} else {
			$delete = '';
		}
		$form_fields['buttons'] = array('tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>$delete</td></tr>\n");
		$form_fields['context'] = array( 'input' => 'hidden', 'value' => 'shiba-add-new-to-gallery' );
		return $form_fields;
	}


	/*
	 * Media Plus initialization functions for general blog pages
	 *
	 * Handles drawing of gallery objects, as well as how to get gallery object contents using get_posts or query_posts.
	 *
	 */

	function init_general() {		
		// mplus get_posts function [allows gallery objects to do grouping with tags]
		add_action('pre_get_posts', array(&$this,'get_posts_init') );		
		$this->permalink_obj->init();	
					
		if (is_admin()) return;

		// Functions to properly show a gallery object in your blog
		add_filter('the_content', array(&$this,'process_gallery_content') );

		// From Shiba Gallery plugin
		add_filter('shiba_get_attachment_link', array(&$this,'filter_attachment_link'), 10, 2);
	}
	
	
	function print_debug($str) {
		if ($this->debug)
			echo "<!-- $str -->\n";
	}


	function substring($str, $startPattern, $endPattern) {
			
		$pos = strpos($str, $startPattern);
		if($pos === false) {
			return "";
		}
	 
		$pos = $pos + strlen($startPattern);
		$temppos = $pos;
		$pos = strpos($str, $endPattern, $pos);
		$datalength = $pos - $temppos;
	 
		$data = substr($str, $temppos , $datalength);
		return $data;
	}



	function javascript_redirect($location) {
		// redirect after header here can't use wp_redirect($location);
		?>
		  <script type="text/javascript">
		  <!--
		  window.location= <?php echo "'" . $location . "'"; ?>;
		  //-->
		  </script>
		<?php
		exit;
	}
	
	
	function filter_attachment_link($link, $id) {
		return preg_replace('/<br\s*?\/+>/', '', $link);
	}

	
	/*
	 * Allow tags for attachments.
	 *
	 */
 
	// Get tag string for a given post id
	 function get_post_tags_string($postID) {
		$tags = wp_get_object_terms( $postID, 'post_tag' );
	
		$tagStr = '';
		if (count($tags)) {
			$tagStr = "{$tags[0]->name}";
			for ($i = 1; $i < count($tags); $i++) 
				$tagStr .= ",{$tags[$i]->name}";
		}
			
		return $tagStr;	
	}
	
	// Get tag-slug string for a given post id - this is required for get_posts
	 function get_post_tags_slug($postID) {
		$tags = wp_get_object_terms( $postID, 'post_tag' );
	
		$tagStr = '';
		if (count($tags)) {
			$tagStr = "'{$tags[0]->slug}'";
			for ($i = 1; $i < count($tags); $i++) 
				$tagStr .= ",'{$tags[$i]->slug}'";
		}
			
		return $tagStr;	
	}


	 
	/*
	 * Tag field menu expansions.
	 *
	 */
 
	// Add tag field for attachment edit menu
	function attachment_fields_to_edit( $form_fields, $post ) {
		$tags = $this->get_post_tags_string($post->ID);
	
		$form_fields['tags'] = array(
			'value' => $tags,
			'label' => __('Attachment Tags'),
			'helps' => __('Associate tags with image attachments to easily include them in multiple image galleries.')
		);
		return $form_fields;
	}
	
	// Save tag field from attachment edit menu
	function attachment_fields_to_save($post, $attachment) {
		$tags = esc_attr($_POST['attachments'][$post['ID']]['tags']);
	
		$tag_arr = explode(',', $tags);
		wp_set_object_terms( $post['ID'], $tag_arr, 'post_tag' );
		return $post;
	}
	

	/*
	 * Display Gallery Objects
	 *
	 * Allows the display of gallery objects similar to attachment objects.
	 * The native WordPress 'gallery' shortcode is used here to display galleries.
	 *
	 */
	
	function process_gallery_content($content) {
		global $post;
		if (is_object($post) && ($post->post_type == 'gallery')) {
			// Fix Twenty Ten theme
			$theme = wp_get_theme();
			if ($theme['Name'] == 'Twenty Ten') { ?>
                <style>
				#content .gallery { margin: 0 0 36px 0; }
				</style>
            <?php }    
			$shortcode = (isset($this->options['shortcode'])) ? stripcslashes($this->options['shortcode']) : '[gallery]';
			$new_content = "<div class='gallery-main' style='text-align:center;'>\n";
			$new_content .= $shortcode;
			$new_content .= "\n</div>\n";
			if (isset($this->options['show_description']) && $this->options['show_description'])
				$new_content .= $content;
			return apply_filters('the_gallery_content', $new_content);
		} 
		return $content;	
	}


	/*
	 * get_posts
	 *
	 * Key gallery object functionality is encapsulated here. 
	 * Get all objects (attachments, posts, pages, and galleries) with tags contained by the gallery, 
	 * and add that to the get_posts results.
	 *
	 */
	
	function posts_where($where, $query) {
		global $wpdb;
		if (!is_array($this->query_args)) return $where;
		
		// Replace post_type
		if (isset($this->query_args['gallery_type']) && $this->query_args['gallery_type']) {
			$type_clause = "{$wpdb->posts}.post_type" . $this->substring($where, "{$wpdb->posts}.post_type", " AND");
			$status_clause = "{$wpdb->posts}.post_status" . $this->substring($where, "{$wpdb->posts}.post_status", " AND");
			
			switch ($this->query_args['gallery_type']) {
			case 'any':
				$where = str_replace($type_clause, "1 = 1", $where);
				break;
			default:	
				$where = str_replace($type_clause, "{$wpdb->posts}.post_type = '{$this->query_args['gallery_type']}'", $where);
			}	
			if ($this->query_args['gallery_type'] != 'attachment') {
				$where = str_replace("AND (post_mime_type LIKE 'image/%')", "", $where);
				$where = str_replace ("{$wpdb->posts}.post_status = 'inherit'", "{$wpdb->posts}.post_status <> 'trash'", $where);
			}	
		}

		if (isset($this->query_args['tag_str']) && $this->query_args['tag_str']) {
			// get id substring
			$id_clause = "{$wpdb->posts}.post_parent" . $this->substring($where, "{$wpdb->posts}.post_parent", " AND");
			$where = str_replace($id_clause, "({$id_clause}OR ({$wpdb->term_taxonomy}.taxonomy = 'post_tag' AND {$wpdb->terms}.slug IN ({$this->query_args['tag_str']})) ) AND {$wpdb->posts}.id <> {$this->query_args['id']}", $where);
		}
		return $where;
	}
	
	function posts_join($join, $query) {
		global $wpdb;
		if (!is_array($this->query_args)) return $join;
		if (!isset($this->query_args['tag_str']) || !$this->query_args['tag_str']) return $join;

		// Must use left join here so that attachments with no tags will also be included
		$join .= "LEFT JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id) LEFT JOIN {$wpdb->term_taxonomy} ON ({$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id) LEFT JOIN {$wpdb->terms} ON ({$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id)";
		return $join;
	}

	function posts_request($request, $query) {
		if (!is_array($this->query_args)) return $request;
		$request = str_replace("SELECT", "SELECT DISTINCT", $request);

		remove_filter('posts_where', array(&$this, 'posts_where'), 10, 2);		
		remove_filter('posts_join', array(&$this, 'posts_join'), 10, 2);		
		remove_filter('posts_request', array(&$this, 'posts_request'), 10, 2);		

		return $request;
	}
			
	function get_posts_init($qobj) {
//		$qobj->query_vars['suppress_filters'] = FALSE;	
		
		$num_images = 0;
		if ($qobj->query_vars['post__in']) // only include specified posts
			return;

		if ($qobj->query_vars['p'])  { // looking for single post		
			$tmp_obj = get_post(absint($qobj->query_vars['p']));
			if (!$tmp_obj) return;
			$qobj->query_vars['post_type'] = $tmp_obj->post_type;
			return;
		}	
						
		//Only process gallery objects
		$objID = $qobj->get('post_parent');
		if (!$objID) return;
		$obj = get_post($objID);
		if (!$obj || !$obj->post_type || !($obj->post_type == 'gallery')) return;

		// Get gallery object tags
		$tag_str = $this->get_post_tags_slug($objID);
		$id = $objID;
		// Get which object type(s) the gallery should contain
		$gallery_type = get_post_meta($obj->ID, '_gallery_type', TRUE);
		if (!$gallery_type) $gallery_type = 'attachment';
		
		// Add gallery tag objects to query	
		$qobj->query_vars['suppress_filters'] = FALSE;	
				
		add_filter('posts_where', array(&$this, 'posts_where'), 10, 2);		
		add_filter('posts_join', array(&$this, 'posts_join'), 10, 2);		
		add_filter('posts_request', array(&$this, 'posts_request'), 10, 2);		

		$this->query_args = compact('id', 'tag_str','gallery_type');
	}
	

	
} // End Shiba_Library class	

endif;

if (class_exists("Shiba_Media_Library")) {
    $shiba_mlib = new Shiba_Media_Library();	
}	
?>