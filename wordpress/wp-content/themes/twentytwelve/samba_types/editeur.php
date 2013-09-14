<?php


function register_editor() {
  $labels = array(
    'name' => 'Editeurs',
    'singular_name' => 'Editeur',
    'add_new' => 'Ajouter',
    'add_new_item' => 'Ajouter un nouvel éditeur',
    'edit_item' => 'Editer l\'éditeur',
    'new_item' => 'Nouvel éditeur',
    'all_items' => 'Tous les éditeurs',
    'view_item' => 'Voir l\'éditeur',
    'search_items' => 'Rechercher un éditeur',
    'not_found' =>  'Aucun éditeur trouvé',
    'not_found_in_trash' => 'Aucun éditeur trouvé dans la corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Editeurs'

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
    'menu_position' => 20,
	'menu_icon' =>  get_bloginfo('template_directory').'/deco/menu_icon_page.png',
    'supports' => array( 'title', 'editor', 'thumbnail' )
  ); 
  register_post_type('editor',$args);
}
add_action( 'init', 'register_editor' );



// 
// //hook into the init action and call create_book_taxonomies when it fires
// add_action( 'init', 'create_jeux_taxonomies', 0 );
// 
// //create two taxonomies, genres and writers for the post type "book"
// function create_jeux_taxonomies() 
// {
//   // Add new taxonomy, make it hierarchical (like categories)
//   $labels = array(
//     'name' => 'Sections',
//     'singular_name' => 'Section',
//     'search_items' =>  'Rechercher une section',
//     'all_items' => 'Toutes les sections',
//     'parent_item' => 'Section parente',
//     'parent_item_colon' => 'Section parente : ',
//     'edit_item' => 'Editer section', 
//     'update_item' => 'Mettre à jour la section',
//     'add_new_item' => "Ajouter une nouvelle section",
//     'new_item_name' => 'Nouvelle section',
//     'menu_name' => "Sections de jeu"
//   ); 	
// 
//   register_taxonomy('section',array('jeux'), array(
//     'hierarchical' => true,
//     'labels' => $labels,
//     'show_ui' => true,
//     'query_var' => true
//   ));
// }



?>