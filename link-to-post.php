<?php
/*
Plugin Name: Link to Post
Plugin URI: http://www.ajcrea.com/plugins/wordpress/plugin-wordpress-lier-un-article-avec-link-to-post.html
Author: Ajcrea
Author URI: http://ajcrea.com
Version: 0.4.1
*/
function adup_option($name,$value){
	if(strlen($value)==0) $value = 'off';
	if(get_option($name) == FALSE)
		add_option($name,$value);
	else
		update_option($name,$value);
}

function pl_init(){
	$locale = get_locale ();
	if ( empty($locale) )
		$locale = 'en_US';

	$mofile = dirname (__FILE__)."/locale/$locale.mo";
	load_textdomain ('link2post', $mofile);
	pl_addbuttons();
}

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
 
function add_pl_tinymce_plugin($plugin_array) {
   $plugin_array['link2post'] = get_bloginfo('wpurl').'/wp-content/plugins/link-to-post/tinymce/editor_plugin.js';
   return $plugin_array;
}

function pl_adminjavascript(){
	?>
	<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/link-to-post/link2post.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	function edlinktopost() {
		var content = getContentSelection(window);
	   tb_show("<?php _e('Link a post','link2post'); ?>","<?php bloginfo('wpurl'); ?>/wp-content/plugins/link-to-post/posts.php?tri="+content+"&amp;validate=1&amp;where=both&amp;category=-1&amp;TB_iframe=true",false);
	}
	function edlinktopage() {
		var content = getContentSelection(window);
	   tb_show("<?php _e('Link a page','link2post'); ?>","<?php bloginfo('wpurl'); ?>/wp-content/plugins/link-to-post/pages.php?tri="+content+"&amp;validate=1&amp;where=both&amp;TB_iframe=true",false);
	}
	//]]>
	</script>	
	<?php
}

function pl_quicktags(){
	$buttonshtml = '<input type="button" class="ed_button" onclick="edlinktopost(); return false;" title="' . __('Link a post','link2post') . '" value="' . __('Link a post','link2post') . '" />';
	$buttonshtml .= '<input type="button" class="ed_button" onclick="edlinktopage(); return false;" title="' . __('Link a page','link2post') . '" value="' . __('Link a page','link2post') . '" />';
	?>
	<script type="text/javascript" charset="utf-8">
	// <![CDATA[
	   (function(){
		  if (typeof jQuery === 'undefined') {
			 return;
		  }
		  jQuery(document).ready(function(){
			 jQuery("#ed_toolbar").append('<?php echo $buttonshtml; ?>');
		  });
	   }());
	// ]]>
	</script>
	<?php
}

function pl_adminpage() {	
	add_options_page('Link to post', 'Link to post', 8, __FILE__, 'pl_optionpage');	
}

function pl_optionpage(){
	if(isset($_POST['Submit'])){
		adup_option('pl_select',$_POST['select']);
		adup_option('pl_nofollow',$_POST['nofollow']);
	}
	$select = get_option('pl_select');
	$nofollow = get_option('pl_nofollow');
	?>
	<div class="wrap">
		<h2><?php echo _e('Options of "Link to post"','link2post'); ?></h2>
		
		<h3><?php _e('Configuration','link2post'); ?></h3>
			<form action="" method="post">
				<p>
					<input type="checkbox" name="select" id="select" <?php if($select == 'on') echo 'checked="checked"'; ?>/>
					<label for="select"><?php _e('Search with selected text','link2post'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="nofollow" id="nofollow" <?php if($nofollow == 'on') echo 'checked="checked"'; ?>/>
					<label for="nofollow"><?php _e('Add the nofollow attribute','link2post'); ?></label>
				</p>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php _e('Save changes','link2post'); ?>" />
				</p>
			</form>
	</div>
	<?php
}
 
add_action('init', 'pl_init');
add_action('admin_menu', 'pl_adminpage');
add_action('admin_print_scripts','pl_adminjavascript');
add_action( 'edit_form_advanced', 'pl_quicktags');
add_action( 'edit_page_form', 'pl_quicktags');

?>