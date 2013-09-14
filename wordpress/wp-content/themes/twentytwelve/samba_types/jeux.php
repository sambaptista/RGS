<?php


function register_jeu() {
  $labels = array(
    'name' => 'Fiche de jeux',
    'singular_name' => 'Fiche de jeu',
    'add_new' => 'Ajouter',
    'add_new_item' => 'Ajouter une nouvelle fiche de jeu',
    'edit_item' => 'Editer la fiche',
    'new_item' => 'Nouvelle fiche',
    'all_items' => 'Toutes les fiches',
    'view_item' => 'Voir la fiche',
    'search_items' => 'Rechercher une fiche',
    'not_found' =>  'Aucune fiche de jeu trouvée',
    'not_found_in_trash' => 'Aucune fiche de jeu trouvée dans la corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Fiches de jeu'

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
	'menu_icon' =>  get_bloginfo('template_directory').'/deco/menu_icon_page.png',
    'supports' => array( 'title', 'editor', 'thumbnail' )
  ); 
  register_post_type('games',$args);
}
add_action( 'init', 'register_jeu' );




//hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_jeux_taxonomies', 0 );

//create two taxonomies, genres and writers for the post type "book"
function create_jeux_taxonomies() 
{
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name' => 'Sections',
		'singular_name' => 'Section',
		'search_items' =>  'Rechercher une section',
		'all_items' => 'Toutes les sections',
		'parent_item' => 'Section parente',
		'parent_item_colon' => 'Section parente : ',
		'edit_item' => 'Editer section', 
		'update_item' => 'Mettre à jour la section',
		'add_new_item' => "Ajouter une nouvelle section",
		'new_item_name' => 'Nouvelle section',
		'menu_name' => "Sections de jeu"
	); 	
	
	register_taxonomy('section',array('games'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true
	));
  
  
  
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
	  'name' => 'Genres',
	  'singular_name' => 'Genre',
	  'search_items' =>  'Rechercher un genre',
	  'all_items' => 'Tous les genres',
	  'parent_item' => 'Genre parent',
	  'parent_item_colon' => 'Genre parent : ',
	  'edit_item' => 'Editer genre', 
	  'update_item' => 'Mettre à jour le genre',
	  'add_new_item' => "Ajouter un nouveau genre",
	  'new_item_name' => 'Nouveau genre',
	  'menu_name' => "Genres de jeu"
	); 	
	
	register_taxonomy('genre',array('games'), array(
	  'hierarchical' => true,
	  'labels' => $labels,
		'show_admin_column' => true,
	  'show_ui' => true,
	  'query_var' => true
	));

  
  
  
  
  
  
  
}







?>