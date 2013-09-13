<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 13.04.2011

/* This is cms.php */

//check that the user is logged in
if(!check_login()) {
	$theme->html_small($login_template);
	echo $theme->display();
	if(isset($_POST['login'])) {
		$login = login($_POST['password'], $password);
		if(!$login) echo "That password is wrong!!!";
		else header('Location: /cms');
	}
}
//if they are logged in
else {
	//get the admin menu
	$theme->html_small($admin_menu_template);
	echo $theme->display();
	$page = (isset($_GET['page'])) ? $_GET['page'] : 'dash';
	$action = (isset($_GET['action'])) ? $_GET['action'] : null;
	
	//which page to show
	switch($page) {
		case 'dash':
			function dir_size($dir) 
			{ 
				$handle = opendir($dir); 
				
				$mas = 0;
				
				while ($file = readdir($handle)) { 
					if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) { 
						$mas += filesize($dir.'/'.$file); 
						} else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') { 
						$mas += dir_size($dir.'/'.$file); 
					} 
				}
				closedir($handle); 
				return $mas; 
			} 
			
			function human_size($size, $decimals = 1) {
				$suffix = array('Bytes','KB','MB','GB','TB',- - 'PB','EB','ZB','YB','NB','DB');
				$i = 0;
				
				while ($size >= 1024 && ($i < count($suffix) - 1)){
				$size /= 1024;
				$i++;
				}
				return round($size, $decimals).' '.$suffix[$i];
			}
			$filesize = human_size(dir_size('/home/emeralds/public_html/'));
			
			$theme->html_small($dashboard_template);
			$theme->replace_tokens(array('SPACE_USED' => $filesize, 'SPACE_MAX' => 500 . ' MB'));
			echo $theme->display();
			break;
		
		case 'posts':
			//which action to take
			switch($action) {
				default:
					$posts = $database->get_data("SELECT title, id, FROM_UNIXTIME(timestamp, '%D %M %Y') as date FROM posts");
					echo '<h1>Posts</h1>';
					echo '<p><a href="' . URL . 'cms/?page=posts&amp;action=add">Add a new post</a></p>';
					foreach($posts as $post) {
						$theme->html_small($post_list_template);
						$theme->replace_tokens(array('POST_TITLE' => $post['title'], 'ID' => $post['id'], 'POST_DATE' =>$post['date']));
						echo $theme->display();	
					}
					break;
					
				case 'add':
					//if the add form has been submitted
					if(isset($_POST['post'])) {
						$post = $_POST['post'];
						$post['content'] = htmlentities($database->sanitize($post['content']));
						$post['time'] = time();
						$add = $database->add_data("INSERT INTO posts (title, content, timestamp) VALUES ('{$post['title']}','{$post['content']}', {$post['time']})");
						if(!$add) {
							$_SESSION['message']['add_error'] = $database->error();
							echo "Error";
							header("Location:" . URL . "cms/?page=posts&action=add");	
						}
						else {
							$post_id = $database->insert_id();
							$_SESSION['message']['success'] = "Post added successfully";
							echo $post_id;
							header("Location:" . URL . "cms/?page=posts&action=update&id=" . $post_id);
						}
					}
					//if the form has not been submitted
					else {
						echo '<h1>Add new post</h1>';
						if(isset($_SESSION['message'])) {
							foreach($_SESSION['message'] as $mess) {
								echo $mess;	
							}
						}
						$theme->html_small($post_update_template);
						$theme->replace_tokens(array('POST_TITLE' => '', 'ID' => '', 'POST_CONTENT' => ''));
						$display = str_replace('Update', 'Add', $theme->display());
						echo $display;
					}
				
					break;
					
				case 'delete':
					if(isset($_POST['delete'])) {
						if(isset($_POST['delete']['yes'])) {
							$del = $database->add_data("DELETE FROM posts WHERE id = {$_GET['id']}");
							header("Location:" . URL . "cms/?page=posts");	
						}
						elseif(isset($_POST['delete']['no'])) {
							header("Location:" . URL . "cms/?page=posts");	
						}
						
					}
					else {
						$post = $database->get_data("SELECT title FROM posts WHERE id = {$_GET['id']}", true);
						$theme->html_small($post_delete_template);
						$theme->replace_tokens(array('POST_TITLE' => $post['title']));
						echo $theme->display();
					}
				
					break;
					
				case 'update':
					//if the update form has been submitted
					if(isset($_POST['post'])) {
						$post = $_POST['post'];
						$post['content'] = htmlentities($database->sanitize($post['content']));
						$update = $database->update_data("UPDATE posts SET title = '{$post['title']}', content = '{$post['content']}' WHERE id = {$post['id']}");
						if(!$update) { 
							$_SESSION['message']['update_error'] = $database->error();
						}
						else {
							$_SESSION['message']['success'] = "Post updated successfully";
						}
						
						header("Location:" . URL . "cms/?page=posts&action=update&id=" . $post['id']);
						
					}
					//if the form hasn't been submitted
					else {
						$post_id = $database->sanitize($_GET['id']);
						$post = $database->get_data("SELECT title, id, FROM_UNIXTIME(timestamp, '%D %M %Y') AS date, content FROM posts WHERE id = $post_id", true, true);
						echo '<h1>Update - ' . $post['title'] . '</h1>';
						if(isset($_SESSION['message'])) {
							foreach($_SESSION['message'] as $mess) {
								echo $mess;	
							}
						}
						$theme->html_small($post_update_template);
						$theme->replace_tokens(array('POST_TITLE' => $post['title'], 'ID' => $post['id'], 'POST_CONTENT' => $post['content']));
						echo $theme->display();
						unset($_SESSION['message']);
					}
				
					break;
			}
			
			
			break;
		
		case 'cats':
			switch($action) {
				default:
					$cats = $database->get_data("SELECT name, id FROM categories");
					echo '<h1>Categories</h1>';
					echo '<p><a href="' . URL . 'cms/?page=cats&amp;action=add">Add a new category</a></p>';
					foreach($cats as $cat) {
						$theme->html_small($cat_list_template);
						$theme->replace_tokens(array('CAT_NAME' => $cat['name'], 'ID' => $cat['id']));
						echo $theme->display();
					}
					break;
					
				case 'add':
					if(isset($_POST['cat'])) {
						$cat = $_POST['cat'];
						$cat['name'] = $database->sanitize($cat['name']);
						if(empty($cat['slug'])) {
							$cat['slug'] = strtolower(str_replace(' ', '_', $cat['name']));
						}
						else {
							$cat['slug'] = $database->sanitize($cat['slug']);	
						}
						$add = $database->add_data("INSERT INTO categories (name, slug) VALUES ('{$cat['name']}', '{$cat['slug']}')");
						if(!$add) {
							$_SESSION['message']['add_error'] = $database->error();
							header("Location:" . URL . "cms/?page=cats&action=add");	
						}
						else {
							$cat_id = $database->insert_id();
							$_SESSION['message']['success'] = "Category added successfully";
							header("Location:" . URL . "cms/?page=cats&action=update&id=$cat_id");	
						}
					}
					else {
						echo '<h1>Add new category</h1>';
						if(isset($_SESSION['message'])) {
							foreach($_SESSION['message'] as $mess) {
								echo $mess;	
							}
						}
						$theme->html_small($cat_update_template);
						$theme->replace_tokens(array('CAT_NAME' => '', 'ID' => '', 'CAT_SLUG' => ''));
						$display = str_replace('Update', 'Add', $theme->display());
						echo $display;	
					}
					break;
					
				case 'update':
					if(isset($_POST['cat'])) {
						$cat = $_POST['cat'];
						$cat['name'] = $database->sanitize($cat['name']);
						if(empty($cat['slug'])) {
							$cat['slug'] = strtolower(str_replace(' ', '_', $cat['name']));	
						} else {
							$cat['slug'] = $database->sanitize($cat['slug']);	
						}
						$update = $database->update_data("UPDATE categories SET name = '{$cat['name']}', slug = '{$cat['slug']}' WHERE id = {$cat['id']}");
						if(!$update) {
							$_SESSION['message']['update_error'] = $database->error();	
						}
						else {
							$_SESSION['message']['success'] = "Category updated successfully";	
						}
						header("Location:" . URL . "cms/?page=cats&action=update&id={$cat['id']}");
					}
					else {
						$cat = $database->get_data("SELECT name, slug FROM categories WHERE id = {$_GET['id']}", true);
						echo '<h1>Update - ' . $cat['name'] . '</h1>';
						if(isset($_SESSION['message'])) {
							foreach($_SESSION['message'] as $mess) {
								echo $mess;	
							}
						}
						$theme->html_small($cat_update_template);
						$theme->replace_tokens(array('CAT_NAME' => $cat['name'], 'ID' => $_GET['id'], 'CAT_SLUG' => $cat['slug']));
						echo $theme->display();
						unset($_SESSION['message']);
					}
					break;
					
				case 'delete':
					if(isset($_POST['delete'])) {
						if(isset($_POST['delete']['yes'])) {
							$del = $database->add_data("DELETE FROM categories WHERE id = {$_GET['id']}");
							$del_pages = $database->add_data("DELETE FROM pages WHERE cat_id = {$_GET['id']}");
							header("Location:" . URL . "cms/?page=cats");	
						}
						elseif(isset($_POST['delete']['no'])) {
							header("Location:" . URL . "cms/?page=cats");	
						}
						
					}
					else {
						$cat = $database->get_data("SELECT name FROM categories WHERE id = {$_GET['id']}", true);
						$theme->html_small($post_delete_template);
						$theme->replace_tokens(array('POST_TITLE' => $cat['name']));
						echo $theme->display();
					}
					break;	
			}
			
			break;
		
		case 'pages':
			switch($action) {
				default:
					$pages = $database->get_data("SELECT p.title, p.id, p.slug, FROM_UNIXTIME(p.timestamp, '%D %M %Y') AS date, c.name FROM pages AS p JOIN categories AS c ON c.id = p.cat_id");
					echo '<h1>Pages</h1>';
					echo '<p><a href="' . URL . 'cms/?page=pages&amp;action=add">Add a new page</a></p>';
					foreach($pages as $page) {
						$theme->html_small($page_list_template);
						$theme->replace_tokens(array('PAGE_TITLE' => $page['title'], 'DATE' => $page['date'], 'ID' => $page['id'], 'CAT' => $page['name'], 'SLUG' => $page['slug']));
						echo $theme->display();	
					}
					break;
					
				case 'add':
					if(isset($_POST['page'])) {
						$page = $_POST['page'];
						$page['content'] = htmlentities($database->sanitize($page['content']));
						if(empty($page['slug'])) {
							$page['slug'] = strtolower(str_replace(' ', '_', $page['title']));	
						}
						$page['time'] = time();
						$add = $database->add_data("INSERT INTO pages (title, content, cat_id, slug, timestamp) VALUES ('{$page['title']}', '{$page['content']}', '{$page['cat']}', '{$page['slug']}', '{$page['time']}')");
						if(!$add) {
							$_SESSION['message']['add_error'] = $database->error();
							header("Location:" . URL . "cms/?page=pages&action=add");	
						} else {
							$_SESSION['message']['success'] = "Page added successfully";
							header("Location:" . URL . "cms/?page=pages&action=update&id=" . $database->insert_id());
							
						}
					}
					else {
						$cats = $database->get_data("SELECT name, id FROM categories ORDER BY name DESC");
						echo '<h1>Add a new page</h1>';
						$categories = "";
						foreach($cats as $cat) {
							$categories .= "<option value=\"{$cat['id']}\">{$cat['name']}</option>";	
						}
						$theme->html_small($page_update_template);
						$theme->replace_tokens(array('PAGE_TITLE' => '', 'PAGE_CONTENT' => '', 'ID' => '', 'CATEGORIES' => $categories, 'PAGE_SLUG' => ''));
						$display = str_replace('Update', 'Add', $theme->display());
						echo $display;
					}
					
					break;
					
				case 'update':
					if(isset($_POST['page'])) {
						$page = $_POST['page'];
						$page['content'] = htmlentities($database->sanitize($page['content']));
						if(empty($page['slug'])) {
							$page['slug'] = strtolower(str_replace(' ', '_', $page['title']));	
						}
						$update = $database->update_data("UPDATE pages SET title = '{$page['title']}', content = '{$page['content']}', cat_id = {$page['cat']}, slug = '{$page['slug']}' WHERE id = {$page['id']}");
						if(!$update) {
							$_SESSION['message']['update_error'] = $database->error();
						} else {
							$_SESSION['message']['success'] = "Page updated successfully";	
						}
						header("Location:" . URL . "cms/?page=pages&action=update&id={$page['id']}");
					}
					else {
						$page = $database->get_data("SELECT p.title, p.content, p.slug, c.name, (SELECT GROUP_CONCAT(name) FROM categories ORDER BY name DESC) AS categories, (SELECT GROUP_CONCAT(id) FROM categories ORDER BY name DESC) AS cat_ids FROM pages AS p JOIN categories AS c ON c.id = p.cat_id WHERE p.id = {$_GET['id']}", true);
						$cats = explode(',', $page['categories']);
						$cat_ids = explode(',', $page['cat_ids']);
						echo "<h1>Update - {$page['title']}</h1>";
						if(isset($_SESSION['message'])) {
							foreach($_SESSION['message'] as $mess) {
								echo $mess;	
							}
						}
						$categories = "";
                        foreach($cats as $key => $cat) {
							if($cat == $page['name']) {
							$categories .= "<option selected value=\"{$cat_ids[$key]}\">$cat</option>";
							} else {
								$categories .= "<option value=\"{$cat_ids[$key]}\">$cat</option>";
							} 
						}
						$theme->html_small($page_update_template);
						$theme->replace_tokens(array('PAGE_TITLE' => $page['title'], 'PAGE_CONTENT' => $page['content'], 'ID' => $_GET['id'], 'CATEGORIES' => $categories, 'PAGE_SLUG' => $page['slug']));
						echo $theme->display();
						unset($_SESSION['message']);
					}
					break;
					
				case 'delete':
					if(isset($_POST['delete'])) {
						if(isset($_POST['delete']['yes'])) {
							$del = $database->add_data("DELETE FROM pages WHERE id = {$_GET['id']}");
							header("Location:" . URL . "cms/?page=pages");	
						}
						elseif(isset($_POST['delete']['no'])) {
							header("Location:" . URL . "cms/?page=pages");	
						}
						
					}
					else {
						$page = $database->get_data("SELECT title FROM pages WHERE id = {$_GET['id']}", true);
						$theme->html_small($post_delete_template);
						$theme->replace_tokens(array('POST_TITLE' => $page['title']));
						echo $theme->display();
					}
					break;	
			}
		
			break;
		
		case 'logout':
			session_destroy();
			echo '<h1>Logout</h1><p>Bye bye!</p>';
			header("Refresh:5; url=" . URL);
			break;	
	}
}
?>