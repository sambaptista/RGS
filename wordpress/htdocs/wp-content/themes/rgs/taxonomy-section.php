<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */



get_header(); 

	// echo '-'.$wp_query->query_vars['section'].'-';
	// echo '-'.$wp_query->query_vars['term'].'-';

	// récupère les articles de type jeux (donc les fiches de jeu) qui appartiennent à la section que nous avons choisi (paradox, 39-15, sc2, ...)
	$section_games = new WP_Query( array(
		'post_type' => 'jeux',
		'section' => $wp_query->query_vars['term'])
	);
	
	// récupère les ids des jeux afin de chercher ensuite les meta data qui y font allusion.
	$games_ids = array();
    while ($section_games->have_posts()) : $section_games->the_post(); 
		array_push($games_ids, get_the_ID());
    endwhile;
	
	// récupère les news qui font référence à ce jeu
	$news = new WP_Query(
        array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'value' => $games_ids
                )
            )
        )
	);
	

	//$items = wp_get_nav_menu_items( $menu, $args );
	
	wp_nav_menu( array(
		'menu' => $wp_query->query_vars['term']
	) );
	

    while ($news->have_posts()) : $news->the_post(); 
		the_title();
		the_content();
		
 		//get_template_part('slide', 'realisationsliees');
		
    endwhile;









get_footer(); 

?>