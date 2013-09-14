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

echo 'section - '.$wp_query->query_vars['taxonomy_name'];




$posts_array = get_posts( $args ); 




$new = new WP_Query('post_type=jeux&section='.$wp_query->query_vars['taxonomy_name'];);

    while ($new->have_posts()) : $new->the_post(); 
		the_title();

     	the_content();

    endwhile;











?>