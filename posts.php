<?php
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');
global $wpdb;
$categories = get_categories();
$nofollow = get_option('pl_nofollow');
$bFirstAndSelect = 0;
if(get_option('pl_select') == 'on' && $_REQUEST['validate'] == 1 && strlen($_REQUEST['tri'])>0)
	$bFirstAndSelect = 1;
?><html>
<head>
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
		<p id="showFilter"><a href="javascript:showFilter()"><?php _e('Show filter','link2post'); ?></a></p>
		<fieldset id="filter">
			<legend><?php _e('Filter posts','link2post'); ?></legend>
			<form action="" method="GET" id="fc">
				<p>
					<label for="tri"><?php _e('Content','link2post'); ?></label>
					<input type="text" name="tri" id="tri" value="<?php if(strlen($_REQUEST['validate'])>1 || $bFirstAndSelect) echo $_REQUEST['tri']; ?>"/>
					<select name="where" id="where">
						<option value="title" <?php if($_REQUEST['where'] == 'title') echo 'selected="selected"'; ?>><?php _e('in title','link2post'); ?></option>
						<option value="content" <?php if($_REQUEST['where'] == 'content') echo 'selected="selected"'; ?>><?php _e('in content','link2post'); ?></option>
						<option value="both" <?php if($_REQUEST['where'] == 'both' || strlen($_REQUEST['where']) == 0) echo 'selected="selected"'; ?>><?php _e('in both','link2post'); ?></option>
					</select>
					<select name="category" id="category">
					<?php foreach($categories as $cat){ if($cat->category_count == 0) continue; ?>
						<option value="<?php echo $cat->term_id; ?>" <?php if($_REQUEST['category'] == $cat->term_id) echo 'selected="selected"'; ?>><?php echo $cat->name; ?></option>
					<?php } ?>
						<option value="-1" <?php if($_REQUEST['category'] == -1 || strlen($_REQUEST['category']) == 0) echo 'selected="selected"'; ?>><?php _e('all categories','link2post'); ?></option>
					</select>
				</p>
				<p id="validate">
					<input type="submit" class="mceButton" name="validate" id="validate" value="<?php _e('search','link2post'); ?>" />
					<?php if(strlen($_REQUEST['tri'])>0 && (strlen($_REQUEST['validate'])>1 || $bFirstAndSelect)){ ?><a href="posts.php"><?php _e('Cancel','link2post'); ?></a><?php } ?>
					<a href="javascript:hideFilter()"><?php _e('Hide filter','link2post'); ?></a>
				</p>
			</form>
		</fieldset>
		<?php

		function pages($nb,$nbpages,$page,$where = 'both',$tri = '',$category = -1){
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
				echo '1 '.__('post','link2post');
			}
			elseif($nb>1){
				echo $nb.' '.__('posts','link2post');
			}
			echo '</span>';
			if($nbpages > 1){
				for($i = 1;$i<=$nbpages;$i++){
					if($nbpages>=8){
						if($page > 4){
							if($i == 1){
								echo '<a href="posts.php?validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&lt;&lt;</a>&nbsp;&nbsp;';
								continue;
							}
							else if($i < $page -3){ continue;}
						}
						if($page < $nbpages - 3){
							if($i == $nbpages){
								echo '<a href="posts.php?validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&gt;&gt;</a>';
								continue;									
							}
							else if($i > $page +3){ continue; }
						}
					}
					if($i == $page){ $bold1 = '<strong>'; $bold2 = '</strong>'; }
					else { $bold1 = $bold2 = ''; }
					echo '<a href="posts.php?validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">'.$bold1.$i.$bold2.'</a>';
					if($i != $nbpages) echo '&nbsp;&nbsp;';
				}
			}
			echo '</p>';
		}

		function pl_trim_excerpt($text) {
			if (strlen($text) > 0 ) {
		 
				$text = strip_shortcodes( $text );
				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]&gt;', $text);
				$text = strip_tags($text);
				$excerpt_length = apply_filters('excerpt_length', 55);
				$words = explode(' ', $text, $excerpt_length + 1);
				if (count($words) > $excerpt_length) {
					array_pop($words);
					array_push($words, '[...]');
					$text = implode(' ', $words);
				}
			}
			return $text;
		}
			$where = $tables = '';
			$type = ' ( post_type = "POST" OR post_type = "PAGE" )';
			if(strlen($_REQUEST['validate'])>1 || $bFirstAndSelect){
				if(strlen($_REQUEST['tri'])>0){
					$mots = explode(' ',trim($_REQUEST['tri']));
					switch($_REQUEST['where']){
						case 'title':
							if(count($mots)>1){
								$where = ' AND ';
								foreach($mots as $key=>$mot){
									if($key == 0) $where .= ' ( ';
									else $where .= ' AND ';
									$where .= ' post_title LIKE "%'.htmlentities($mot).'%" ';
									if($key == count($mots) - 1) $where .= ' ) ';
								}
							}
							else
								$where = ' AND post_title LIKE "%'.htmlentities($_REQUEST['tri']).'%" ';
							
						break;
						case 'content':
							if(count($mots)>1){
								$where = ' AND ';
								foreach($mots as $key=>$mot){
									if($key == 0) $where .= ' ( ';
									else $where .= ' AND ';
									$where .= ' post_content LIKE "%'.htmlentities($mot).'%" ';
									if($key == count($mots) - 1) $where .= ' ) ';
								}
							}
							else
							$where = ' AND post_content LIKE "%'.htmlentities($_REQUEST['tri']).'%" ';
						break;
						case 'both':
							if(count($mots)>1){
								$where = ' AND ';
								foreach($mots as $key=>$mot){
									if($key == 0) $where .= ' ( ';
									else $where .= ' AND ';
									$where .= ' ( post_title LIKE "%'.htmlentities($mot).'%" OR post_content LIKE "%'.htmlentities($mot).'%" ) ';
									if($key == count($mots) - 1) $where .= ' ) ';
								}
							}
							else
							$where = ' AND ( post_title LIKE "%'.htmlentities($_REQUEST['tri']).'%" OR post_content LIKE "%'.htmlentities($_REQUEST['tri']).'%" ) ';
						break;
					}
				}
				switch($_REQUEST['category']){
					case -1:
						
					break;
					default:
						$tables = ', '.$wpdb->terms.' as t, '.$wpdb->term_taxonomy.' as tt,'.$wpdb->term_relationships.' as tr ';
						$where .= ' AND ID = object_id AND t.term_id = tt.term_id AND t.term_id = '.$_REQUEST['category'].' AND tt.term_taxonomy_id = tr.term_taxonomy_id ';
				}
			}
			$result = $wpdb->get_results('SELECT COUNT( * ) AS num_posts FROM '.$wpdb->posts.$tables.' WHERE  post_type = "POST" '.$where.' AND post_status = "publish" ');
			$nb = $result[0]->num_posts;
			$number = 15;
			if(!isset($_GET['page'])){ $page = 1; }
			else{ $page = $_GET['page']; }
			$offset = $number * ($page-1);
			$nbpages = ceil($nb/$number);
			$posts = $wpdb->get_results('SELECT * FROM '.$wpdb->posts.$tables.' WHERE post_type = "POST" '.$where.' AND post_status = "publish" ORDER BY post_date desc LIMIT '.$offset.','.$number.'');
			if(count($posts)>0){
				pages($nb,$nbpages,$page,$_REQUEST['where'],$_REQUEST['tri'],$_REQUEST['category']);
				echo '<ul id="liens">';
				foreach($posts as $post){
					$GLOBALS['post'] = $post;
					echo '<li><a href="'.get_permalink($post->ID).'" onclick="return insertPostLink(this,\''.$nofollow.'\')" title="'.pl_trim_excerpt($post->post_content).'">'.get_the_title($post->ID).'</a></li>';
				}
				echo '</ul>';
			}
			else{
				?><p><span class="results"><?php _e('No post','link2post'); ?></span></p><?php	
			}
		?>
</div>
</body>
</html>