<?php
/*
Plugin Name: Like Posts and Comments
Description: Add a button for like any post (or custom post type) and comment
Author: Aurélien Chappard
Author URI: http://www.deefuse.fr/
Version: 1.1
Network: false
License: GPL
*/
if( !class_exists( 'LikePostAndComments' ) ) {
	require dirname(__FILE__) . "/settings.php";
	
	class LikePostAndComments {  
		var $pluginPath;
		var $pluginUrl;
		var $postLiKeTable;
		var $commentLiKeTable;
		
		public function __construct()  
		{  
			// Set Plugin Path
			$this->pluginPath = dirname(__FILE__);

			// Set Plugin URL
			$this->pluginUrl = WP_PLUGIN_URL . '/likes-posts-comments';

			//
			$this->postLiKeTable = "likes_posts";
			$this->commentLiKeTable = "likes_comments";
			
			//echo basename($this->pluginPath).'/languages';
			load_plugin_textdomain('likes-posts-comments', false, basename($this->pluginPath).'/languages' );
			
			//Settings
			new LikePostAndCommentsSetting();
			
			$this->actions();
			
		} 
		
		public function getLinkLikePost()
		{
			$labelLikePost = get_option('like_post_label_'.get_current_blog_id());
			$labelUnLikePost = get_option('unlike_post_label_'.get_current_blog_id());
			
			global $wpdb;
			$like_post_table_name = $wpdb->prefix . $this->postLiKeTable;
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM $like_post_table_name WHERE users_ID = %d AND post_ID = %d", wp_get_current_user()->ID, get_the_ID() ); 
			
			$userlike = $wpdb->get_var( $sql );
			if($userlike == 0){
				$toreturn = '<a href="#" rel="postID_'.get_the_ID().'" class="likes-post-link dolike">'.$labelLikePost.'</a>';
				$toreturn = apply_filters('the_link_like_post', $toreturn, get_the_ID(), $labelLikePost);
				return $toreturn;
			}else
			{
				$toreturn = '<a href="#" rel="postID_'.get_the_ID().'" class="likes-post-link doUnlike">'.$labelUnLikePost.'</a>';
				$toreturn = apply_filters('the_link_like_post', $toreturn, get_the_ID(), $labelUnLikePost);
				return $toreturn;
			}			
		}
		
		public function getLinkLikeComment()
		{
			$labelLikeComment = get_option('like_comment_label_'.get_current_blog_id());
			$labelUnLikeComment = get_option('unlike_comment_label_'.get_current_blog_id());
			
			global $wpdb;
			$like_post_table_name = $wpdb->prefix . $this->commentLiKeTable;
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM $like_post_table_name WHERE users_ID = %d AND comment_ID = %d", wp_get_current_user()->ID, get_comment_ID() ); 
			
			$userlike = $wpdb->get_var( $sql );
			if($userlike == 0){
				$toreturn = '<a href="#" rel="commentID_'.get_comment_ID().'" class="likes-comment-link dolike">'.$labelLikeComment.'</a>';
				$toreturn = apply_filters('the_link_like_comment', $toreturn, get_comment_ID(), $labelLikeComment);
				return $toreturn;
			}else
			{
				$toreturn = '<a href="#" rel="commentID_'.get_comment_ID().'" class="likes-comment-link doUnlike">'.$labelUnLikeComment.'</a>';
				$toreturn = apply_filters('the_link_like_comment', $toreturn, get_comment_ID(), $labelUnLikeComment);
				return $toreturn;
			}			
		}
		
		public function getLinkNbLikePost()
		{
			$post_id = get_the_ID();
			return '<span class="nb-likes-post" id="nbLikePost_'.$post_id.'">' . $this->getTextNbLikePost($post_id) . '</span>';
		}
		public function getLinkNbLikeComment()
		{
			$comment_id = get_comment_ID();
				return '<span class="nb-likes-comment" id="nbLikeComment_'.$comment_id.'">' . $this->getTextNbLikeComment($comment_id) . '</span>';
		}
		
		
		public function	getTextNbLikePost($postId){
			global $wpdb;
			$like_post_table_name = $wpdb->prefix . $this->postLiKeTable;
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM $like_post_table_name WHERE post_ID = %d", $postId ); 
			
			$nblikes = $wpdb->get_var( $sql );
			
			if($nblikes == 0){
				$toreturn = __('Be the first to like this post','likes-posts-comments');
				$toreturn = apply_filters('the_number_like_post', $toreturn, $postId, $nblikes);
				return $toreturn; 
			}
			else
			{
				$toreturn = sprintf( _n( 'One person like', '%1$s persons like', $nblikes, 'likes-posts-comments' ),number_format_i18n( $nblikes) );
				$toreturn = apply_filters('the_number_like_post', $toreturn, $postId, $nblikes);
				return $toreturn;
			}
		}
		public function getTextNbLikeComment($commentId){
			global $wpdb;
			$like_comment_table_name = $wpdb->prefix . $this->commentLiKeTable;
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM $like_comment_table_name WHERE comment_ID = %d", $commentId ); 
			$nblikes = $wpdb->get_var( $sql );
			
			if($nblikes == 0){
				$toreturn = __('Be the first to like this comment','likes-posts-comments');
				$toreturn = apply_filters('the_number_like_comment', $toreturn, $commentId, $nblikes);
				return  $toreturn;
			}else{
				$toreturn = sprintf( _n( 'One person like', '%1$s persons like', $nblikes, 'likes-posts-comments' ),number_format_i18n( $nblikes) );
				$toreturn = apply_filters('the_number_like_comment', $toreturn, $commentId, $nblikes);
				return  $toreturn;
			}
		}
		/**
		 * Action callback for the plugin
		 */
		private function actions()
		{
			add_action( 'wp_head', array( &$this, 'add_ajax_library' ) );
			//add_action('admin_menu', array(&$this,'create_admin_page_option'));
			//add_action('admin_init', array(&$this,'setup_plugin_options'));
			add_action('wp_enqueue_scripts', array(&$this,'register_plugin_scripts'));
			add_action( 'wp_ajax_like_a_post', array( &$this, 'like_a_post' ) );
			add_action( 'wp_ajax_like_a_comment', array( &$this, 'like_a_comment' ) );
			add_action('delete_comment', array( &$this, 'delete_comment' ) );
			add_action('delete_post', array( &$this, 'delete_post' ) );
			add_action( 'delete_blog', array( &$this, 'cleardatabase'), 10, 2 );
		}
		/**
		* @param int $blog_id Blog ID
		* @param bool $drop True if blog's table should be dropped. Default is false.
		*/
		public function cleardatabase( $blog_id, $drop ) 
		{
			error_log( ">>> Supression d'un site : ".$blog_id );
			global $wpdb;
			$old_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
			
			$like_post_table_name = $wpdb->prefix . 'likes_posts';
			$like_comment_post_table_name = $wpdb->prefix . 'likes_comments';

			$sql = "DROP TABLE $like_post_table_name, $like_comment_post_table_name";
			$wpdb->query($sql);
			
			// Delete all option plugins
			delete_option('like_post_label_'.$blog_id);
			delete_option('unlike_post_label_'.$blog_id);
			delete_option('like_comment_label_'.$blog_id);
			delete_option('unlike_comment_label_'.$blog_id);
			
			switch_to_blog($old_blog);
			
		}
		
		public function delete_comment($comment_id){
			global $wpdb;
			
			$like_comment_table_name = $wpdb->prefix . $this->commentLiKeTable;
			$wpdb->query( 
				$wpdb->prepare( 
					"
					DELETE FROM $like_comment_table_name
					 WHERE comment_ID = %d",
						$comment_id 
					)
			);
			//echo "suppression d'un commentaire : ".$comment_id."<br/>";
		}
		
		public function delete_post($post_id){
			global $wpdb;
			
			$like_post_table_name = $wpdb->prefix . $this->postLiKeTable;
			$wpdb->query( 
				$wpdb->prepare( 
					"
					DELETE FROM $like_post_table_name
					 WHERE post_ID = %d",
						$post_id 
					)
			);
			//echo "suppression d'un commentaire : ".$comment_id."<br/>";
		}
		
		public function like_a_comment(){
			if( is_user_logged_in()){
				
				// First, we need to make sure the post ID parameter has been set and that it's a numeric value
				if( isset( $_POST['comment_id'] ) && is_numeric( $_POST['comment_id'] ) ) {
					global $wpdb;
					
					$like_comment_table_name = $wpdb->prefix . $this->commentLiKeTable;
					
					// Insert a new like
					if( isset( $_POST['actionLike'] )){
						if(($_POST['actionLike'] == "doLike")  ) {
							$res = $wpdb->insert($like_comment_table_name, array(
								'users_ID' => wp_get_current_user()->ID,
								'comment_ID' => $_POST['comment_id'] ));
							
						}elseif ($_POST['actionLike'] == "doUnLike") {
							
							$res = $wpdb->query( 
								$wpdb->prepare( 
									"
									DELETE FROM $like_comment_table_name
									 WHERE users_ID = %d
									 AND comment_ID = %d
									",
										wp_get_current_user()->ID, $_POST['comment_id'] 
									)
							);
						}
						if($res){
							$response = array('success' => true);
							$response['nbLikeText'] = $this->getTextNbLikeComment($_POST['comment_id']);
						}else
						{
							$response = array('success' => false);
						}
						header( "Content-Type: application/json" );
						$response = json_encode($response );
						echo $response;
					}
					die();
				}
				die();
			}			
			die();
		}
		
		public function like_a_post(){
			if( is_user_logged_in()){
				
				// First, we need to make sure the post ID parameter has been set and that it's a numeric value
				if( isset( $_POST['post_id'] ) && is_numeric( $_POST['post_id'] ) ) {
					global $wpdb;
					
					$like_post_table_name = $wpdb->prefix . $this->postLiKeTable;
					
					// Insert a new like
					if( isset( $_POST['actionLike'] )){
						if(($_POST['actionLike'] == "doLike")  ) {
							$res = $wpdb->insert($like_post_table_name, array(
								'users_ID' => wp_get_current_user()->ID,
								'post_ID' => $_POST['post_id'] ));
							
						}elseif ($_POST['actionLike'] == "doUnLike") {
							
							$res = $wpdb->query( 
								$wpdb->prepare( 
									"
									DELETE FROM $like_post_table_name
									 WHERE users_ID = %d
									 AND post_ID = %d
									",
										wp_get_current_user()->ID, $_POST['post_id'] 
									)
							);
						}
						
						if($res){
							$response = array('success' => true);
							$response['nbLikeText'] = $this->getTextNbLikePost($_POST['post_id']);
							//echo "1";
						}else
						{
							$response = array('success' => false);
							//echo "-1";
						}
						header( "Content-Type: application/json" );
						$response = json_encode($response );
						echo $response;
					}
					die();
				}
				die();
			}			
			die();
		}
		/**
		* Adds the WordPress Ajax Library to the frontend.
		*/
	   public function add_ajax_library() {

		   $html = '<script type="text/javascript">';
			   $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';
			   $html .= 'var likePostLabel = "' . htmlentities( get_option('like_post_label_'.get_current_blog_id()) ) . '";';
			   $html .= 'var unLikePostLabel = "' . htmlentities( get_option('unlike_post_label_'.get_current_blog_id()) ) . '";';
			   $html .= 'var likeCommentLabel = "' . htmlentities( get_option('like_comment_label_'.get_current_blog_id()) ) . '";';
			   $html .= 'var unLikeCommentLabel = "' . htmlentities( get_option('unlike_comment_label_'.get_current_blog_id())) . '";';
		   $html .= '</script>';

		   echo $html;

	   }
		
		public function register_plugin_scripts(){
			
			//wp_register_script( 'likes_posts_comments_js', $this->pluginUrl . '/js/likes.js' , array( 'jquery' ) );
			wp_enqueue_script( 'likes_posts_comments_js', $this->pluginUrl . '/js/likes.js' , array( 'jquery' ), '1.0', true );
		}
			
		/**
		 * Setup for the plugins
		 */
		public function setup($networkwide)
		{
			global $wpdb;
			// On a cliqué sur le bouton d'activation en mode super admin sur l'ensemble du réseau
			if (function_exists('is_multisite') && is_multisite()) {
				if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blogids as $blog_id) {
						error_log( ">>> setup pour multisite: ".$blog_id );
						switch_to_blog($blog_id);
						$this->installDBTable($blog_id);
					}
					switch_to_blog($old_blog);
					return;
				}else
				{
					$this->installDBTable(get_current_blog_id());
				}
			}
			
			else{
				$this->installDBTable(get_current_blog_id());
			}
			
		}
		
		/**
		 * Create table in database
		 * @global type $wpdb
		 */
		private function installDBTable($blog_id)
		{
			global $wpdb;
			$usersTableName = $wpdb->users;
			$commentsTableName = $wpdb->comments;
			$postTableName = $wpdb->posts;

			
			$like_post_table_name = $wpdb->prefix . $this->postLiKeTable;
			$comment_post_table_name = $wpdb->prefix . $this->commentLiKeTable;
			
			if (!empty ($wpdb->charset))
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			if (!empty ($wpdb->collate))
				$charset_collate .= " COLLATE {$wpdb->collate}";
			
			$sql = "CREATE TABLE $like_post_table_name(
				users_ID BIGINT(20) UNSIGNED NOT NULL,
				post_ID BIGINT(20) UNSIGNED NOT NULL ,
				PRIMARY KEY (users_ID, post_ID),
				KEY key_users_ID_$blog_id (users_ID ASC) ,
				KEY key_postID_$blog_id (post_ID ASC),
				CONSTRAINT like_post_users_$blog_id
					FOREIGN KEY ( users_ID )
					REFERENCES $usersTableName (ID)
					ON DELETE NO ACTION
					ON UPDATE NO ACTION,
				CONSTRAINT like_post_post_$blog_id
					FOREIGN KEY ( post_ID )
					REFERENCES $postTableName (ID)
					ON DELETE NO ACTION
					ON UPDATE NO ACTION) ENGINE=InnoDB $charset_collate;

				CREATE TABLE $comment_post_table_name(
				users_ID BIGINT(20) UNSIGNED NOT NULL,
				comment_ID BIGINT(20) UNSIGNED NOT NULL ,
				PRIMARY KEY (users_ID, comment_ID),
				KEY key_users_ID_$blog_id (users_ID ASC) ,
				KEY key_commentID_$blog_id (comment_ID ASC),
				CONSTRAINT like_comment_user_$blog_id
					FOREIGN KEY ( users_ID )
					REFERENCES $usersTableName (ID)
					ON DELETE NO ACTION
					ON UPDATE NO ACTION,
				CONSTRAINT like_comment_comment_$blog_id
					FOREIGN KEY ( comment_ID )
					REFERENCES $commentsTableName (comment_ID)
					ON DELETE NO ACTION
					ON UPDATE NO ACTION) ENGINE=InnoDB $charset_collate;";
			
			//echo $sql;
			error_log( ">>> installDBTable blog_id =  $blog_id <<<<" );
			error_log( ">>> $sql" );
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}  
}

if( class_exists( 'LikePostAndComments' ) ) {
	require_once dirname( __FILE__ ) . '/template-tags.php';
	$likePostAndComments_plugin = new LikePostAndComments();
	//echo "<pre>";print_r($likePostAndComments_plugin);echo "</pre>";
	register_activation_hook( __FILE__, array( &$likePostAndComments_plugin, 'setup' ));
}