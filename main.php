<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 13.04.2011

/* This is main.php */

switch($show) {
	case 'posts':
		//get the data from the database
		$posts = $database->get_data("SELECT title, content, FROM_UNIXTIME(timestamp, '%D %M %Y') as date FROM posts ORDER BY timestamp DESC", false, true);
		
		//process the HTML template
		echo '<h1>Latest news</h1>';
		foreach($posts as $post) {
			$theme->html_small($post_template);
			$theme->replace_tokens(array('POST_TITLE' => $post['title'], 'POST_CONTENT' => $post['content'], 'POST_DATE' => $post['date']));
			echo $theme->display();
		}
		
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