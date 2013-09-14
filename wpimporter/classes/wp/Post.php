<?php


class Post
{
	
	
	
	public function __construct($array)
	{
		
	}
	
	
	
	
	
	/*
	* Url doc : http://codex.wordpress.org/Function_Reference/wp_insert_post
	*
	* fonction : wp_insert_post()
	*
	*$post = array(
	*  'ID'             => [ <post id> ] //Are you updating an existing post?
	*  'menu_order'     => [ <order> ] //If new post is a page, it sets the order in which it should appear in the tabs.
	*  'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
	*  'ping_status'    => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
	*  'pinged'         => [ ? ] //?
	*  'post_author'    => [ <user ID> ] //The user ID number of the author.
	*  'post_category'  => [ array(<category id>, <...>) ] //post_category no longer exists, try wp_set_post_terms() for setting a post's categories
	*  'post_content'   => [ <the text of the post> ] //The full text of the post.
	*  'post_date'      => [ Y-m-d H:i:s ] //The time post was made.
	*  'post_date_gmt'  => [ Y-m-d H:i:s ] //The time post was made, in GMT.
	*  'post_excerpt'   => [ <an excerpt> ] //For all your post excerpt needs.
	*  'post_name'      => [ <the name> ] // The name (slug) for your post
	*  'post_parent'    => [ <post ID> ] //Sets the parent of the new post.
	*  'post_password'  => [ ? ] //password for post?
	*  'post_status'    => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' | 'custom_registered_status' ] //Set the status of the new post.
	*  'post_title'     => [ <the title> ] //The title of your post.
	*  'post_type'      => [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may want to insert a regular post, page, link, a menu item or some custom post type
	*  'tags_input'     => [ '<tag>, <tag>, <...>' ] //For tags.
	*  'to_ping'        => [ ? ] //?
	*  'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
	*);
	*/
	
	public static function insertPost($post)
	{
		
	}

	
	
	
	
	
}





?>