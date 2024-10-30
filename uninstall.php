<?php 
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

global $wpdb;
if (function_exists('is_multisite') && is_multisite()) {
	$old_blog = $wpdb->blogid;
	$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	foreach ($blogids as $blog_id) {
		switch_to_blog($blog_id);
		uninstallDBTable();
		delete_all_option($blog_id);
	}
	switch_to_blog($old_blog);
	return;
}else
{
	uninstallDBTable(get_current_blog_id());
}

function uninstallDBTable(){
	global $wpdb;
	$like_post_table_name = $wpdb->prefix . 'likes_posts';
	$like_comment_post_table_name = $wpdb->prefix . 'likes_comments';
	
	$sql = "DROP TABLE IF EXISTS $like_post_table_name, $like_comment_post_table_name";
	$wpdb->query($sql);
}
function delete_all_option($blog_id){
	// Delete all option plugins
	delete_option('like_post_label_'.$blog_id);
	delete_option('unlike_post_label_'.$blog_id);
	delete_option('like_comment_label_'.$blog_id);
	delete_option('unlike_comment_label_'.$blog_id);
}