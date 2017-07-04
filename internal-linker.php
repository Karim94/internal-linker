<?php

/*
Plugin Name: internal-linker
Description: WordPress plugin which injects internal links into post content automatically.
Author: Karim94
Author URI: https://github.com/Karim94
Plugin URI: https://github.com/Karim94/internal-linker
License: MIT
License URI: https://github.com/Karim94/internal-linker/blob/master/LICENSE
Version: 1.0
Text Domain: il
*/

/**
 * Processes content by adding internal links when a tag's name is used in the content of a post
 */
function il_add_links($content){

	// Get post
	$post = get_post();
	if (!empty($post)){

		// Gets all Blog Tags
		$tags = get_terms(array(
						    'taxonomy' => 'post_tag',
						    'hide_empty' => true,
						));
		
		// For each Tag
		// Checks if the post contains the Tag-Name
		foreach ($tags as $tag){
			$tag_name = $tag->name;

			$content = str_replace(
						$tag_name,
						il_get_latest_post_link($tag),
						$content);
		}
	}

	return $content;
}

/**
 *  Finds the link to the latest post containing a specific $tag
 * @param  $tag
 * @return $latest_link_html	Link of latest post with tag name
 * 		     $tag_name 					Returns tag name when no post is found
 */
function il_get_latest_post_link($tag){

	$tag_slug = $tag->slug;
	$tag_name = $tag->name;

	$this_post_date = get_the_date();
	
	// Get posts before date of this post
	$args = array(
		'tag' 		=> $tag_slug,
		'date_query'=> array(
	    'before'    => get_the_date(),
	  )
  );

	$the_query = new WP_Query($args);
	
	// Gets a link to the first post with the same tag
	if ($the_query->have_posts()){
		$post_with_tag = $the_query->posts[0];
		$permalink = get_permalink($post_with_tag->ID);
		$latest_link_html = "<a href='$permalink'>$tag_name</a>";

		return $latest_link_html;
	}
	else {
		return $tag_name;
	}
}

/**
 * Calls the function 'il_add_links' before saving post content to database
 */
add_filter('content_save_pre','il_add_links');

?>