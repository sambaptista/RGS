<?php


function register_attachment() {
  $labels = array(
    'name' => 'Pièces jointes',
    'singular_name' => 'Pièce jointe',
    'add_new' => 'Ajouter',
    'add_new_item' => 'Ajouter une nouvelle pièce jointe',
    'edit_item' => 'Editer la pièce jointe',
    'new_item' => 'Nouvelle pièce jointe',
    'all_items' => 'Toutes les pièces jointes',
    'view_item' => 'Voir la pièce jointe',
    'search_items' => 'Rechercher une pièce jointe',
    'not_found' =>  'Aucune pièce jointe trouvée',
    'not_found_in_trash' => 'Aucune pièce jointe trouvée dans la corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Pièces jointes'

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
  register_post_type('custom_attachment',$args);
}
add_action( 'init', 'register_attachment' );




// //hook into the init action and call create_book_taxonomies when it fires
// add_action( 'init', 'create_portfolio_taxonomies', 0 );
// 
// //create two taxonomies, genres and writers for the post type "book"
// function create_portfolio_taxonomies() 
// {
//   // Add new taxonomy, make it hierarchical (like categories)
//   $labels = array(
//     'name' => 'Domaines',
//     'singular_name' => 'Domaine',
//     'search_items' =>  'Rechercher un domaine',
//     'all_items' => 'Tous les domaines',
//     'parent_item' => 'Domaine parent',
//     'parent_item_colon' => 'Domaine parent : ',
//     'edit_item' => 'Editer domaine', 
//     'update_item' => 'Mettre à jour le domaine',
//     'add_new_item' => "Ajouter un nouveau domaine d'activité",
//     'new_item_name' => 'Nouveau nom de domaine',
//     'menu_name' => "Domaines d'activité"
//   ); 	
// 
//   register_taxonomy('domaine',array('jeux'), array(
//     'hierarchical' => true,
//     'labels' => $labels,
//     'show_ui' => true,
//     'query_var' => true,
//     'rewrite' => array( 'slug' => 'domaine' ),
//   ));
// }
// 


?>