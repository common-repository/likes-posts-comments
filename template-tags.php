<?php

/**
 * Template tag: Boxed Style Paging
 *
 * @param array $args:
 *	'before': (string)
 *	'after': (string)
 *	'options': (string|array) Used to overwrite options set in WP-Admin -> Settings -> PageNavi
 *	'query': (object) A WP_Query instance
 */
function wp_postlike() {
	if(is_user_logged_in()){
		global $likePostAndComments_plugin;
		
		echo $likePostAndComments_plugin->getLinkLikePost();
	}
}
function wp_nb_postlike() {
	if(is_user_logged_in()){
		global $likePostAndComments_plugin;
		
		echo $likePostAndComments_plugin->getLinkNbLikePost();
	}
}

function wp_commentlike() {
	if(is_user_logged_in()){
		global $likePostAndComments_plugin;
		echo $likePostAndComments_plugin->getLinkLikeComment();
	}
}

function wp_nb_commentlike() {
	if(is_user_logged_in()){
		global $likePostAndComments_plugin;
		
		echo $likePostAndComments_plugin->getLinkNbLikeComment();
	}
}
?>
