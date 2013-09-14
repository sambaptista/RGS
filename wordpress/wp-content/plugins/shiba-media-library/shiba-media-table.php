<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


if (!class_exists("Shiba_Media_List_Table")) :
	if(!class_exists('WP_Media_List_Table')){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-media-list-table.php' );
	}
	class Shiba_Media_List_Table extends WP_Media_List_Table { 
		public $is_trash = FALSE; 
		
		// for wordpress 3.1 and above. Replaces edit-attachment-rows.php
		function display_media_table() { 
			// filling in arguments needed from get_column_info
			global $post;
			$redir_tab = 'gallery';
			$this->_column_headers = array($this->get_columns(), array(), array() /*$this->get_sortable_columns()*/);
			extract( $this->_args );
//          $this->display_tablenav( 'top' );
	
			?>
				<table class="<?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
				<thead>
				<tr><?php $this->print_column_headers(); ?></tr>
				</thead>
	
				<tfoot>
				<tr><?php $this->print_column_headers( false ); ?></tr>
				</tfoot>
	
				<tbody id="the-list"<?php if ( $singular ) echo " class='list:$singular'"; ?>>
					<?php 
					global $wp_query, $menu_order;
					// order posts by menu order
					if (is_array($menu_order) /*&& !isset($_GET['orderby'])*/) {
						usort($wp_query->posts, array($this, 'menu_order_cmp')); 
						unset($menu_order);
					}	
					$this->display_rows_or_placeholder(); ?>
				</tbody>
			</table>
			<?php 
//			$this->display_tablenav( 'bottom' );
 	}
	
	function menu_order_cmp($a, $b) {
		global $menu_order;

   		$pos1 = isset($menu_order[$a->ID]) ? $menu_order[$a->ID] : 0;
   		$pos2 = isset($menu_order[$b->ID]) ? $menu_order[$b->ID] : 0;

   		if ($pos1==$pos2)
       		return 0;
  		 else
      		return ($pos1 < $pos2 ? -1 : 1);
	}

}
endif;
?>