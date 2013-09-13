<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 13.04.2011

/* This is main.php */

switch($show) {
	case 'posts':
		//get the data from the database
		$all_posts = $database->get_data("SELECT title, content, FROM_UNIXTIME(timestamp, '%D %M %Y') as date FROM posts ORDER BY timestamp DESC", false, true);
		
                $all_posts_count = count($all_posts);
                
                $last_page = ceil($all_posts_count / 5);
                
                $posts = array_chunk($all_posts, 5);
                
                $page_count = count($posts);
                
                $p = (isset($queries[1])) ? $queries[1]-1 : 0;
                
                if($p > $last_page) $p = $last_page;
                
		//process the HTML template
		echo '<h1>Latest news</h1>';
		foreach($posts[$p] as $post) {
			$theme->html_small($post_template);
			$theme->replace_tokens(array('POST_TITLE' => $post['title'], 'POST_CONTENT' => $post['content'], 'POST_DATE' => $post['date']));
			echo $theme->display();
		}
                echo '<p>';
                if($p > 0) {
                    echo '<a href="/posts/1"><<</a> [...]';
                }
                $range = 3;
		
                for($i = (($p + 1) - $range); $i < (($p + $range) + 2); $i++) {
                    if($i >= 1 && $i <= $page_count) {
                        if($i == $p+1) {
                            echo ' <b>' . $i . '</b> ';
                        } else {
                            echo ' <a href="/posts/' . $i . '">' . $i . '</a> ';
                        }
                    }
                }
                
                if(($p + 1) != $last_page) {
                    echo '[...] <a href="/posts/' . $last_page . '">>></a>';
                }
                
                echo '</p>';
		break;
	
	case 'cat':
		//get the data from the database
		$pages = $database->get_data("SELECT c.name, p.title, p.id, p.slug FROM categories AS c JOIN pages AS p ON p.cat_id = c.id WHERE c.slug = '{$queries[1]}'");
		
		//include the HTML template
		echo "<h1>{$pages[0]['name']}</h1>";
		foreach($pages as $single) {
			$theme->html_small($cat_template);
			$theme->replace_tokens(array('PAGE_SLUG' => $single['slug'], 'PAGE_TITLE' => $single['title']));
			echo $theme->display();
		}
		
		break;
		
	case 'pages':
		//get the data from the database
		$page = $database->get_data("SELECT p.title, p.content, c.slug, c.name, FROM_UNIXTIME(p.timestamp, '%D %M %Y') as date FROM pages AS p JOIN categories AS c ON c.id = p.cat_id WHERE p.slug = '{$queries[1]}'", true, true, 'page');
		
		$breadcrumbs = "<a href=\"" . URL . "cat/{$page['slug']}\">{$page['name']}</a> - {$page['title']}";
		
		//process the HTML template
		$theme->html_small($page_template);
		$theme->replace_tokens(array('PAGE_TITLE' => $page['title'], 'PAGE_CONTENT' => $page['content'], 'BREAD_CRUMBS' => $breadcrumbs));
		echo $theme->display();
		break;
}
?>