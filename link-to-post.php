<?php
/*
Plugin Name: Link to Post
Plugin URI: http://www.ajcrea.com/plugins/wordpress/plugin-wordpress-lier-un-article-avec-link-to-post.html
Author: Ajcrea
Author URI: http://ajcrea.com
Version: 0.2
*/
function pl_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "add_pl_tinymce_plugin");
		add_filter('mce_buttons', 'register_pl_button');
   }
}
 
function register_pl_button($buttons) {
   array_push($buttons, "separator", "post_link","page_link");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_pl_tinymce_plugin($plugin_array) {
   $plugin_array['link2post'] = get_bloginfo('wpurl').'/wp-content/plugins/link-to-post/tinymce/editor_plugin.js';
   return $plugin_array;
}
 
// init process for button control
add_action('init', 'pl_addbuttons');

?>