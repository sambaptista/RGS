<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Media_Library_Helper")) :

class Shiba_Media_Library_Helper {
	
	function admin_init() {
		global $shiba_mlib;

		add_action('admin_head', array($this,'mlib_header'));
		// Add Insert into Post button
		add_filter('attachment_fields_to_edit', array($this, 'add_insert_into_post_button'), 10, 2);
		add_filter('wp_redirect', array($this,'gallery_redirect'), 10, 2);

		if (defined('DOING_AJAX')) {
//			trigger_error('doing ajax' . print_r($_REQUEST, TRUE));
			if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'inline-save')) {
				switch ($_REQUEST['screen']) {
				case 'edit-gallery':
					if (!class_exists("Shiba_Media_Library_Manage")) 
						require(SHIBA_MLIB_DIR . '/shiba-mlib-manage.php');
					$shiba_mlib->manage = new Shiba_Media_Library_Manage();	
					break;
				}
			}
		} else
			add_filter('current_screen', array($this, 'current_screen') );

	}

	function current_screen($screen) {
		global $shiba_mlib;
		
		switch ($screen->id) {
		case 'upload':
			require_once(SHIBA_MLIB_DIR.'/shiba-media-table.php');
			if (class_exists("Shiba_Media_List_Table"))
				$shiba_mlib->media_table = new Shiba_Media_List_Table();	
			require_once(SHIBA_MLIB_DIR.'/shiba-mlib-upload.php');
			if (class_exists("Shiba_Media_Library_Upload"))
				$shiba_mlib->upload = new Shiba_Media_Library_Upload();	

			add_action('admin_head', array(&$this,'upload_header'), 51);
			// Adds tag column to the media library page
			// Give it a higher priority so that it runs first before other plugins 
			// that may add new columns	
			add_filter('manage_media_columns', array($this,'add_admin_columns'), 5 ); 
			add_action('manage_media_custom_column', array($this,'manage_admin_columns'), 10, 2);
//			add_filter('post_updated_messages', array(&$this,'gallery_updated_messages'));
			break;
		case 'edit-gallery':
			if (!class_exists("Shiba_Media_Library_Manage")) 
				require(SHIBA_MLIB_DIR . '/shiba-mlib-manage.php');
			$shiba_mlib->manage = new Shiba_Media_Library_Manage();	
			add_action('admin_head', array($this,'upload_header'), 51);
			add_action('admin_head', array($this,'gallery_header'), 51);

			break;
		case 'post':
			if (isset($_GET['post'])) {
				$post_id = abs($_GET['post']);
				$post_type = get_post_type($post_id);
				if ($post_type != 'gallery') break;
			} else break;
		case 'gallery':
			require_once(SHIBA_MLIB_DIR.'/shiba-media-table.php');
			if (class_exists("Shiba_Media_List_Table"))
				$shiba_mlib->media_table = new Shiba_Media_List_Table();	
			require_once(SHIBA_MLIB_DIR . '/shiba-mlib-add.php');
			if (class_exists("Shiba_Media_Library_Add")) 
				$shiba_mlib->add = new Shiba_Media_Library_Add();	

			add_action('admin_head', array($this,'upload_header'), 51);
			add_filter('manage_media_columns', array($this,'add_admin_columns'), 5 ); 
			add_action('manage_media_custom_column', array($this,'manage_admin_columns'), 10, 2);
			break;
		}				
		return $screen;
	}

	// Captures delete permanently link from new gallery screen
	function gallery_redirect($location, $status) {
		if (isset($_GET['action']) && isset($_GET['post']) && ($_GET['action'] == 'delete') && 
			strpos($location, 'edit.php?post_type=attachment&deleted=1') !== FALSE) {
			// redirect to http referer
			$referer = wp_get_referer();
			$location = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids', 'posted'), $referer );
			$location = add_query_arg('message', 12, $location);
		}
		return $location;
	}	 

		
	function add_pages() {
		global $shiba_mlib;
		
		$url = 'edit.php?post_type=gallery'; //menu_page_url('gallery');
		// Add a new submenu under Gallery:
		$shiba_mlib->options_page = add_submenu_page( $url, 'Options', 'Options', 'install_plugins', 'shiba_media_options', array(&$this,'media_options_page') );

	}


	function media_options_page() {
		include('shiba-mlib-options.php');
	}

	function mlib_header() {
		global $post_type;
		// icons are 16x16
		?>
		<style>		
		#adminmenu #menu-posts-gallery div.wp-menu-image{background:transparent url('<?php echo get_bloginfo('url');?>/wp-admin/images/menu.png') no-repeat scroll -121px -33px;}
		#adminmenu #menu-posts-gallery:hover div.wp-menu-image,#adminmenu #menu-posts-gallery.wp-has-current-submenu div.wp-menu-image{background:transparent url('<?php echo get_bloginfo('url');?>/wp-admin/images/menu.png') no-repeat scroll -121px -1px;}		
        </style>
        <?php
	}
	
	function gallery_header() {
	?>
	<style>
	#icon-edit { background:transparent url('<?php echo get_bloginfo('url');?>/wp-admin/images/icons32.png') no-repeat -251px -5px; }
	.fixed .column-id { width: 50px; }
	.fixed .column-images { width: 70px; text-align: center; }
	.fixed .column-gallery_categories, .fixed .column-gallery_tags { width: 15%; }
 	</style>
  
    <?php
	}
	
	// Javascript for processing the added Media Library bulk-action form in the Media>> Library screen
	function upload_header() {
	?>
	<style>
	.fixed .column-new_icon, .fixed .column-icon { width: 70px; text-align: center; }
	.fixed .column-title, .fixed .column-new_title { width: 25%; }
	.fixed .column-date { width: 96px; }
	.fixed .column-parent { width: 20%; }
	.fixed .column-att_tag { width: 15%; }
	</style>
	<?php
	}
		
	function add_insert_into_post_button($form_fields, $post) {
		$attachment_id = $post->ID; $filename = basename( $post->guid );
		$calling_post_id = 0;
		if ( isset( $_GET['post_id'] ) )
			$calling_post_id = absint( $_GET['post_id'] );
		elseif ( isset( $_POST ) && count( $_POST ) ) // Like for async-upload where $_GET['post_id'] isn't set
			$calling_post_id = $post->post_parent;

		$post_type = get_post_type($calling_post_id);
		$insert_media_button_array = apply_filters('shiba_insert_media_button', array('post','page'));
		if ($calling_post_id && in_array($post_type, $insert_media_button_array))
			$send = "<input type='submit' class='button' name='send[$attachment_id]' value='" . esc_attr__( 'Insert into Post' ) . "' />";
		else return $form_fields; //$send = '';	
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

		$thumbnail = '';
		if ( (strpos($post->post_mime_type, 'image') !== FALSE) && $calling_post_id &&  current_theme_supports( 'post-thumbnails') &&  get_post_thumbnail_id( $calling_post_id ) != $attachment_id ) {
			$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$calling_post_id" );
			$thumbnail = "<a class='wp-post-thumbnail' id='wp-post-thumbnail-" . $attachment_id . "' href='#' onclick='WPSetAsThumbnail(\"$attachment_id\", \"$ajax_nonce\");return false;'>" . esc_html__( "Use as featured image" ) . "</a>";
		}
		
		$form_fields['buttons'] = array('tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>$send $thumbnail $delete</td></tr>\n");
		return $form_fields;		
	}

	// Add tag column to the attachment Media Library page
	function add_admin_columns($posts_columns) {
		global $current_screen;		
		$new_columns['cb'] = '<input type="checkbox" />';
		if (isset($current_screen) && isset($current_screen->id) && ($current_screen->id == 'upload')) {
			$new_columns['icon'] = '';
			if (isset($posts_columns['media'])) // For WP 3.0
				$new_columns['media'] = _x( 'File', 'column name' );
			else $new_columns['title'] = _x( 'File', 'column name' );
		} else {
			$new_columns['new_icon'] = '';
			$new_columns['new_title'] = _x( 'File', 'column name' );
		}		
		$new_columns['author'] = __( 'Author' );
		/* translators: column name */
		$new_columns['parent'] = _x( 'Attached to', 'column name' );
//			$new_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
		/* translators: column name */
		$new_columns['date'] = _x( 'Date', 'column name' );
		$new_columns['att_tag'] = _x( 'Tags', 'column name' );

		return $new_columns;
	}
	
	function manage_admin_columns($column_name, $id) {
		global $post, $shiba_mlib;
		
		switch($column_name) {
		case 'att_tag':
			$tagparent = "upload.php?";
			$tags = get_the_tags();
			if ( !empty( $tags ) ) {
				$out = array();
				foreach ( $tags as $c )
					$out[] = "<a href='".$tagparent."tag=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
				echo join( ', ', $out );
			} else {
				_e('No Tags');
			}
			break;
			
		case 'new_icon':
			$attachment_id = 0;
			if ($post->post_type == 'attachment')
				$attachment_id = $post->ID;
			 else if (function_exists('get_post_thumbnail_id')) 
				$attachment_id = get_post_thumbnail_id($post->ID);
			
			// wp_mime_type_icon throws a notice error in 3.1 RC2 when wp_get_attchment_image is called
			if (!$attachment_id) {
				echo '<img width="46" height="60" src="'.includes_url('images/crystal/default.png').'" class="attachment-80x60" />';

				break;
			}		
			if ( $thumb = wp_get_attachment_image( $attachment_id, array(80, 60), true ) ) {
				if ( $post->post_status == 'trash' ) echo $thumb;
				else {
				$attachment = get_post($attachment_id);	
				echo '	
				<a href="media.php?action=edit&amp;attachment_id='.$attachment_id.'" title="'.esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $attachment->post_title)).'">';
				echo $thumb;
				echo "</a>\n";
				}
			}
			break;		

		case 'new_title':
			$att_title = _draft_or_post_title();
			?>
			<strong><a href="<?php echo get_edit_post_link( $post->ID, true ); ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ); ?>"><?php echo $att_title; ?></a></strong>
				<p> <?php
				if ($post->post_type == 'attachment') {
					if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) )
						echo esc_html( strtoupper( $matches[1] ) );
					else 
						echo strtoupper( str_replace( 'image/', '', get_post_mime_type() ) ); 
				} else {
					echo strtoupper($post->post_type);
				}
				?>
				</p>
			<?php 
			if (class_exists('Shiba_Media_List_Table') && isset($shiba_mlib->media_table)) {
				echo $shiba_mlib->media_table->row_actions( $shiba_mlib->media_table->_get_row_actions( $post, $att_title ) ); 
			}
			break;
	
		default:
				break;
		} // end switch
	}
} // end Shiba_Media_Library_Helper class
endif;


?>