<?php
require_once('../../../../wp-blog-header.php');
global $wpdb;
$categories = get_categories();
?><html>
<head>
<script type='text/javascript' src='js/jquery.js'></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
<script type="text/javascript" src="js/link2post.js"></script>
<link rel='stylesheet' href='css/link2post.css' type='text/css' />
</head>
<body>
<div class="tabs">
	<ul>
		<li id="lier_tab" class="current"><span><?php _e('Lier un article','link2post'); ?></span></li>
	</ul>
</div>
<div class="panel_wrapper">
	<div id="lier_panel" class="panel current">
		<p id="showFilter"><a href="javascript:showFilter()"><?php _e('Afficher le filtre','link2post'); ?></a></p>
		<fieldset id="filter">
			<legend><?php _e('Filtrer les articles','link2post'); ?></legend>
			<form action="" method="GET" id="fc">
				<p>
					<label for="tri"><?php _e('Contenu :','link2post'); ?></label>
					<input type="text" name="tri" id="tri" value="<?php echo $_REQUEST['tri']; ?>"/>
					<select name="where" id="where">
						<option value="title" <?php if($_REQUEST['where'] == 'title') echo 'selected="selected"'; ?>><?php _e('dans le titre','link2post'); ?></option>
						<option value="content" <?php if($_REQUEST['where'] == 'content') echo 'selected="selected"'; ?>><?php _e('dans le contenu','link2post'); ?></option>
						<option value="both" <?php if($_REQUEST['where'] == 'both' || strlen($_REQUEST['where']) == 0) echo 'selected="selected"'; ?>><?php _e('les deux','linnk2post'); ?></option>
					</select>
					<select name="category" id="category">
					<?php foreach($categories as $cat){ if($cat->category_count == 0) continue; ?>
						<option value="<?php echo $cat->term_id; ?>" <?php if($_REQUEST['category'] == $cat->term_id) echo 'selected="selected"'; ?>><?php echo $cat->name; ?></option>
					<?php } ?>
						<option value="-1" <?php if($_REQUEST['category'] == -1 || strlen($_REQUEST['category']) == 0) echo 'selected="selected"'; ?>><?php _e('Toutes les catégories','link2post'); ?></option>
					</select>
				</p>
				<p id="validate">
					<input type="submit" class="mceButton" name="validate" id="validate" value="<?php _e('Valider','link2post'); ?>" />
					<?php if(strlen($_REQUEST['validate'])>0){ echo '<a href="posts.php">'.__('Annuler','link2post').'</a>'; } ?>
					<a href="javascript:hideFilter()"><?php _e('Cacher le filtre','link2post'); ?></a>
				</p>
			</form>
		</fieldset>
		<?php

		function pages($nb,$nbpages,$page,$where,$tri){
			echo '<p>';
			echo '<span class="results">';
			if($nb==1){
				echo '1 '.__('article','link2post');
			}
			elseif($nb>1){
				echo $nb.' '.__('articles','link2post');
			}
			echo '</span>';
			if($nbpages > 1){
				for($i = 1;$i<=$nbpages;$i++){
					if($nbpages>=8){
						if($page > 4){
							if($i == 1){
								echo '<a href="posts.php?validate=1&where='.$where.'&tri='.$tri.'&page='.$i.'">&lt;&lt;</a>&nbsp;&nbsp;';
								continue;
							}
							else if($i < $page -3){ continue;}
						}
						if($page < $nbpages - 3){
							if($i == $nbpages){
								echo '<a href="posts.php?validate=1&where='.$where.'&tri='.$tri.'&page='.$i.'">&gt;&gt;</a>';
								continue;									
							}
							else if($i > $page +3){ continue; }
						}
					}
					if($i == $page){ $bold1 = '<strong>'; $bold2 = '</strong>'; }
					else { $bold1 = $bold2 = ''; }
					echo '<a href="posts.php?validate=1&where='.$where.'&tri='.$tri.'&page='.$i.'">'.$bold1.$i.$bold2.'</a>';
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
			if(strlen($_REQUEST['validate'])>0){
				if(strlen($_REQUEST['tri'])>0){
					switch($_REQUEST['where']){
						case 'title':
							$where = ' AND post_title LIKE "%'.htmlentities($_REQUEST['tri']).'%" ';
						break;
						case 'content':
							$where = ' AND post_content LIKE "%'.htmlentities($_REQUEST['tri']).'%" ';
						break;
						case 'both':
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
			$offset = $number * ($page-1);
			$nbpages = ceil($nb/$number);
			$posts = $wpdb->get_results('SELECT * FROM '.$wpdb->posts.$tables.' WHERE post_type = "POST" '.$where.' AND post_status = "publish" ORDER BY post_date desc LIMIT '.$offset.','.$number.'');
			if(count($posts)>0){
				pages($nb,$nbpages,$page,$_REQUEST['where'],$_REQUEST['tri']);
				echo '<ul id="liens">';
				foreach($posts as $post){
					$GLOBALS['post'] = $post;
					echo '<li><a href="'.get_permalink($post->ID).'" onclick="return insertPostLink(this)" title="'.pl_trim_excerpt($post->post_content).'">'.$post->post_title.'</a></li>';
				}
				echo '</ul>';
			}
			else{
				echo '<p><span class="results">'.__("Aucun article","link2post").'</span></p>';		
			}
		?>
	</div>
</div>
</body>
</html>