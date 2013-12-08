<?php


function register_test() {
  $labels = array(
    'name' => 'Tests',
    'singular_name' => 'Test',
    'add_new' => 'Ajouter',
    'add_new_item' => 'Ajouter une nouvelle fiche de test',
    'edit_item' => 'Editer la fiche',
    'new_item' => 'Nouvelle fiche',
    'all_items' => 'Toutes les fiches',
    'view_item' => 'Voir la fiche',
    'search_items' => 'Rechercher une fiche',
    'not_found' =>  'Aucune fiche de test trouvée',
    'not_found_in_trash' => 'Aucune fiche de test trouvée dans la corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Tests'

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
    'menu_position' => 5,
	'menu_icon' =>  get_bloginfo('template_directory').'/style/deco/menu_icon_page.png',
    'supports' => array( 'title', 'editor', 'thumbnail' )
  ); 
  register_post_type('test',$args);
}
add_action( 'init', 'register_test' );






?>