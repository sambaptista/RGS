<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script><![endif]-->
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style/css/style.css"/>
</head>


<body <?php body_class('js'); ?>>

<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
    <a class='btn btn-link' href="/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
</nav>

<div id="page" class="container hfeed site">

		<nav id="section-nav-bar" role="navigation">
			<?php
                wp_nav_menu( array(
                    'theme_location' => 'js',
                    //'depth' => 1,
                    'container' => '',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                ));
            ?>

            <?php get_search_form(); ?>
		</nav><!-- #site-navigation -->

	<div id="main" class="wrapper">