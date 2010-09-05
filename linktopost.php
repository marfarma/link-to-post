<?php
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');
global $wpdb;
$categories = get_categories();
$nofollow = get_option('pl_nofollow');
$shortcode = get_option('pl_shortcode');
$bFirstAndSelect = 0;

$validate = htmlentities(sanitize_text_field($_REQUEST['validate']));
$tri = htmlentities(sanitize_text_field($_REQUEST['tri']));
$where = htmlentities(sanitize_text_field($_REQUEST['where']));
$category = htmlentities(sanitize_text_field($_REQUEST['category']));
$type = htmlentities(sanitize_text_field($_REQUEST['type']));
if(strlen($type) == 0){
	$type = get_option('pl_defaultab'); 
	if(!$type){
		$type = 'post';
	}
} 

if(get_option('pl_select') == 'on' && $validate == 1 && strlen($tri)>0)
	$bFirstAndSelect = 1;
	
function pages($nb,$nbpages,$page,$where = 'both',$tri = '',$category = -1,$type = 'post'){
	global $bFirstAndSelect;
	if(strlen($_REQUEST['validate'])>1 || $bFirstAndSelect)
		$tri = $tri;
	else $tri = '';
	if(strlen($where)==0)
		$where = 'both';
	if(strlen($category)==0)
		$category = -1;
	echo '<p>';
	echo '<span class="results">';
	if($nb==1){
		echo '1 '; _e('result','link2post'); 
	}
	elseif($nb>1){
		echo $nb.' '; _e('results','link2post'); 
	}
	echo '</span>';
	if($nbpages > 1){
		for($i = 1;$i<=$nbpages;$i++){
			if($nbpages>=8){
				if($page > 4){
					if($i == 1){
						echo '<a href="linktopost.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&lt;&lt;</a>&nbsp;&nbsp;';
						continue;
					}
					else if($i < $page -3){ continue;}
				}
				if($page < $nbpages - 3){
					if($i == $nbpages){
						echo '<a href="linktopost.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&gt;&gt;</a>';
						continue;									
					}
					else if($i > $page +3){ continue; }
				}
			}
			if($i == $page){ $bold1 = '<strong>'; $bold2 = '</strong>'; }
			else { $bold1 = $bold2 = ''; }
			echo '<a href="linktopost.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">'.$bold1.$i.$bold2.'</a>';
			if($i != $nbpages) echo '&nbsp;&nbsp;';
		}
	}
	echo '</p>';
}	
	
?><html>
<head>
<title>Link to Post</title>
<script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js'></script>
<script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/quicktags.js'></script>
<script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/thickbox/thickbox.js'></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/link-to-post/link2post.js"></script>
<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/wp-admin.css" type="text/css">
<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/css/colors-fresh.css" type="text/css">
<link rel='stylesheet' href='<?php bloginfo('wpurl'); ?>/wp-content/plugins/link-to-post/tinymce/css/link2post.css' type='text/css' />
</head>
<body>

<div class="wrapper_quicktag">
<p><strong><?php _e('To insert the link, click on the item of your choice','link2post'); ?></strong></p>
<div class="tabs">
	<ul>
		<li id="post_tab" <?php if($type == 'post') echo 'class="current"'; ?>><span><a href="linktopost.php?type=post&validate=<?php echo $validate; ?>&where=<?php echo $where; ?>&tri=<?php echo $tri; ?>&category=<?php echo $category; ?>"><?php _e('Post','link2post'); ?></a></span></li>
		<li id="page_tab" <?php if($type == 'page') echo 'class="current"'; ?>><span><a href="linktopost.php?type=page&validate=<?php echo $validate; ?>&where=<?php echo $where; ?>&tri=<?php echo $tri; ?>&category=<?php echo $category; ?>"><?php _e('Page','link2post'); ?></a></span></li>
		<li id="category_tab" <?php if($type == 'category') echo 'class="current"'; ?>><span><a href="linktopost.php?type=category&validate=<?php echo $validate; ?>&where=<?php echo $where; ?>&tri=<?php echo $tri; ?>&category=<?php echo $category; ?>"><?php _e('Category','link2post'); ?></a></span></li>		
		<li id="tag_tab" <?php if($type == 'tag') echo 'class="current"'; ?>><span><a href="linktopost.php?type=tag&validate=<?php echo $validate; ?>&where=<?php echo $where; ?>&tri=<?php echo $tri; ?>&category=<?php echo $category; ?>"><?php _e('Tag','link2post'); ?></a></span></li>		
	</ul>
</div>
<div class="panel_wrapper">
	<div id="post_panel" class="panel<?php if($type == 'post') echo ' current'; ?>">
		<?php include('tinymce/post.php'); ?>
	</div>
	<div id="page_panel" class="panel<?php if($type == 'page') echo ' current'; ?>">
		<?php include('tinymce/page.php'); ?>
	</div>
	<div id="category_panel" class="panel<?php if($type == 'category') echo ' current'; ?>">
		<?php include('tinymce/category.php'); ?>
	</div>
	<div id="tag_panel" class="panel<?php if($type == 'tag') echo ' current'; ?>">
		<?php include('tinymce/tag.php'); ?>
	</div>
</div>
</body>
</html>