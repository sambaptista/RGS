<?php

function register_video() {
  $labels = array(
    'name' => 'Vidéos',
    'singular_name' => 'Vidéo',
    'add_new' => 'Ajouter',
    'add_new_item' => 'Ajouter une nouvelle vidéo',
    'edit_item' => 'Editer la vidéo',
    'new_item' => 'Nouvelle vidéo',
    'all_items' => 'Toutes les vidéos',
    'view_item' => 'Voir la vidéo',
    'search_items' => 'Rechercher une vidéo',
    'not_found' =>  'Aucune vidéo trouvée',
    'not_found_in_trash' => 'Aucune vidéo trouvée dans la corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Vidéos'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
	'show_in_nav_menus' => true,
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => 15,
	'menu_icon' =>  get_bloginfo('template_directory').'/deco/menu_icon_media.png',
    'supports' => array( 'title', 'editor', 'thumbnail' )
  ); 
  register_post_type('video',$args);
}
add_action( 'init', 'register_video' );

?>