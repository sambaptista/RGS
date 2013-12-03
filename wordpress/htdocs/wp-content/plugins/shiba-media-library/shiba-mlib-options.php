<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if ( ! current_user_can('switch_themes') )
	wp_die(__('You are not allowed to change media settings.'));


global $shiba_mlib;

if (isset($_POST['save_options'])) {
	check_admin_referer('shiba_media_options');

	$location = 'edit.php?post_type=gallery&page=shiba_media_options'; 
	if ( $referer = wp_get_referer() ) {
		if ( FALSE !== strpos( $referer, $location ) ) 
			$location = remove_query_arg( array('message'), $referer );
	}

	unset($_POST['_wpnonce'], $_POST['_wp_http_referer'], $_POST['save_theme_options']);
	update_option('shiba_mlib_options', $_POST);
	$location = add_query_arg('message', 1, $location);
	
	$shiba_mlib->javascript_redirect($location);
	exit;
}

$messages[1] = __('Shiba Media Library settings updated.', 'shiba_mlib');

if ( isset($_GET['message']) && (int) $_GET['message'] ) {
	$message = $messages[$_GET['message']];
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}

$title = __('Shiba Media Options');
?>
<div class="wrap">   
    <?php screen_icon(); ?>
    <h2><?php echo esc_html( $title ); ?></h2>
 <!--   <div style="height:30px;"></div> -->

	<?php
		if ( !empty($message) ) : 
		?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php 
		endif; 
		$options = get_option('shiba_mlib_options');
		if (!is_array($options)) $options = array();
		$shortcode = (isset($options['shortcode'])) ? stripcslashes($options['shortcode']) : '[gallery]';
	?>

    <form name="validate_links" id="validate_links" method="post" action="" class="">
        <?php wp_nonce_field('shiba_media_options'); ?> 

        <div style="clear:both; width:590px;">
		<div class="shiba-field">
			<h3>Customize Gallery Page</h3>
			<input style="width:100%;"  type="text" name="shortcode" value='<?php echo esc_attr($shortcode);?>'/> 
           <small><p>Customize the default gallery object page. Uses <a href="http://codex.wordpress.org/Gallery_Shortcode" target="_blank">WordPress gallery shortcode syntax</a>.</p></small>
		</div>
 		<p><input type="checkbox" name="show_description" <?php if (isset($options['show_description'])) echo 'checked';?>/>  Show Gallery Description</p>
       
        <input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
<!--        <input type="submit" class="button" name="clear_cache" value="<?php esc_attr_e('Clear Cache'); ?>" /> -->

    </form>
    <div style="height:50px;clear:both;"></div>
	
</div>


