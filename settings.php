<?php

if( !class_exists( 'LikePostAndCommentsSetting' ) ) {
	
	class LikePostAndCommentsSetting {  
		
		var $settingOptionPage;
		
		public function __construct()  
		{  
			
			$this->init();
			$this->actions();
		}
		
		private function init(){
			$this->settingOptionPage = "likes_options_page";
		}
		
		private function actions(){
			add_action('admin_menu', array(&$this,'create_admin_page_option'));
			add_action('admin_init', array(&$this,'setup_plugin_options'));
		}
		
		
		/**
		 * Setup all the plugins settings through the Wordpress Settings API
		 */
		public function setup_plugin_options()
		{
			// Define the default option setting for unlike_cap
			if (FALSE == get_option('like_post_label_'.get_current_blog_id())){	add_option( 'like_post_label_'.get_current_blog_id(), __('I like this post', 'likes-posts-comments'));  }
			if (FALSE == get_option('unlike_post_label_'.get_current_blog_id())){	add_option( 'unlike_post_label_'.get_current_blog_id(), __('I do not like this post', 'likes-posts-comments'));  }
			if (FALSE == get_option('like_comment_label_'.get_current_blog_id())){	add_option( 'like_comment_label_'.get_current_blog_id(), __('I like this comment', 'likes-posts-comments'));  }
			if (FALSE == get_option('unlike_comment_label_'.get_current_blog_id())){	add_option( 'unlike_comment_label_'.get_current_blog_id(), __('I do not like this comment', 'likes-posts-comments'));  }
			// First, we register a section. This is necessary since all future options must belong to one.  
			add_settings_section(
				'likes_options_plugin_section',						// ID used to identify this section and with which to register options  
				__('Likes management', 'likes-posts-comments'),		// Title to be displayed on the administration page  
				array(&$this,'description_likes_settingd_section_callback'),			// Callback used to render the description of the section  
				$this->settingOptionPage							// Page on which to add this section of options  
			); 
			
			// Next, we will introduce the fields for toggling the visibility of content elements.  

			//post
			add_settings_field(   
				'like_post_label_'.get_current_blog_id(),						// ID used to identify the field throughout the theme  
				__('Label for post like:', 'likes-posts-comments'),						// The label to the left of the option interface element  
				array(&$this,'field_desc_post_like_label_callback'),		// The name of the function responsible for rendering the option interface  
				$this->settingOptionPage,			// The page on which this option will be displayed  
				'likes_options_plugin_section',				// The name of the section to which this field belongs  
				array()									// Arg  
			); 
					
			add_settings_field(   
				'unlike_post_label_'.get_current_blog_id(),						// ID used to identify the field throughout the theme  
				__('Label for post unlike:', 'likes-posts-comments'),						// The label to the left of the option interface element  
				array(&$this,'field_desc_post_unlike_label_callback'),		// The name of the function responsible for rendering the option interface  
				$this->settingOptionPage,			// The page on which this option will be displayed  
				'likes_options_plugin_section',				// The name of the section to which this field belongs  
				array()									// Arg  
			); 
			
			//comment
			add_settings_field(   
				'like_comment_label_'.get_current_blog_id(),						// ID used to identify the field throughout the theme  
				__('Label for comment like:', 'likes-posts-comments'),						// The label to the left of the option interface element  
				array(&$this,'field_desc_comment_like_label_callback'),		// The name of the function responsible for rendering the option interface  
				$this->settingOptionPage,			// The page on which this option will be displayed  
				'likes_options_plugin_section',				// The name of the section to which this field belongs  
				array()									// Arg  
			); 
			add_settings_field(   
				'unlike_comment_label_'.get_current_blog_id(),						// ID used to identify the field throughout the theme  
				__('Label for comment unlike:', 'likes-posts-comments'),						// The label to the left of the option interface element  
				array(&$this,'field_desc_comment_unlike_label_callback'),		// The name of the function responsible for rendering the option interface  
				$this->settingOptionPage,			// The page on which this option will be displayed  
				'likes_options_plugin_section',				// The name of the section to which this field belongs  
				array()									// Arg  
			); 
			
			// Finally, we register the fields with WordPress  
			register_setting('likes_options_plugin_section', 'like_post_label_'.get_current_blog_id(), array(&$this,'sanitize_like_post_label'));
			register_setting('likes_options_plugin_section', 'unlike_post_label_'.get_current_blog_id(), array(&$this,'sanitize_unlike_post_label'));
			register_setting('likes_options_plugin_section', 'like_comment_label_'.get_current_blog_id(), array(&$this,'sanitize_like_comment_label'));
			register_setting('likes_options_plugin_section', 'unlike_comment_label_'.get_current_blog_id(), array(&$this,'sanitize_unlike_comment_label'));
		}
		
		public function sanitize_like_post_label($input){
			$return = sanitize_text_field($input);
			if($return == "")
				$return  = __('I like this post', 'likes-posts-comments');
			
			return $return;
		}
		public function sanitize_unlike_post_label($input){
			$return = sanitize_text_field($input);
			if($return == "")
				$return  = __('I do not like this post', 'likes-posts-comments');
			
			return $return;
		}
		public function sanitize_like_comment_label($input){
			$return = sanitize_text_field($input);
			if($return == "")
				$return  = __('I like this comment', 'likes-posts-comments');
			
			return $return;
		}
		public function sanitize_unlike_comment_label($input){
			$return = sanitize_text_field($input);
			if($return == "")
				$return  = __('I do not like this comment', 'likes-posts-comments');
			
			return $return;
		}
		
		
		public function description_likes_settingd_section_callback()
		{
			_e("You can customize the labels for each option", "likes-posts-comments");
			 
		}
		
		
		public function field_desc_post_like_label_callback(){
			?>
			<input type="text" class="regular-text" name="like_post_label_<?php echo get_current_blog_id(); ?>" value="<?php echo get_option('like_post_label_'.get_current_blog_id()); ?>" />
			<?php
		}
		public function field_desc_post_unlike_label_callback(){
			?>
			<input type="text" class="regular-text" name="unlike_post_label_<?php echo get_current_blog_id(); ?>" value="<?php echo get_option('unlike_post_label_'.get_current_blog_id()); ?>" />
			<?php
		}
		public function field_desc_comment_like_label_callback(){
			?>
			<input type="text" class="regular-text" name="like_comment_label_<?php echo get_current_blog_id(); ?>" value="<?php echo get_option('like_comment_label_'.get_current_blog_id()); ?>" />
			<?php
		}
		public function field_desc_comment_unlike_label_callback(){
			?>
			<input type="text" class="regular-text" name="unlike_comment_label_<?php echo get_current_blog_id(); ?>" value="<?php echo get_option('unlike_comment_label_'.get_current_blog_id()); ?>" />
			<?php
		}
		
		/**
		 * Add an option page for the settings
		 */
		public function create_admin_page_option()
		{
			add_options_page(__('Likes Posts & Comments Options', 'likes-posts-comments'), 'Likes Posts & Comments', 'activate_plugins', $this->settingOptionPage, array(&$this, 'printAdminPage'));
		}
		
		/**
		 * Setting page output
		 */
		public function printAdminPage(){
			?>
			<div class=wrap>
				<?php screen_icon(); ?>
				<h2><?php _e('Likes Posts & Comments Options', 'likes-posts-comments') ?></h2>
				  
				<form method="post" action="options.php">  
					<?php settings_fields( 'likes_options_plugin_section' ); ?>  
					<?php do_settings_sections( $this->settingOptionPage ); ?>             
					<?php submit_button(); ?> 
				</form>
			</div>
			<?php
		}
	}
}