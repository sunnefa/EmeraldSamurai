<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 13.04.2011

/* This is theme/theme.php */

//the url
$url = str_replace('.com/', '.com', URL);

//the HTML template for pages
$page_template = <<<EOT
<h1>{PAGE_TITLE}</h1>
<p class="breadcrumbs">{BREAD_CRUMBS}<p>
{PAGE_CONTENT}
EOT;

//the HTML template for posts
$post_template = <<<EOT
<h2>{POST_TITLE}</h2>
{POST_CONTENT}
<p class="date">Posted on {POST_DATE}</p>
EOT;

//the HTML template for categories
$cat_template = <<<EOT
<p><a href="$url/pages/{PAGE_SLUG}">{PAGE_TITLE}</a></p>
EOT;

//the HTML template for the menu
$menu_template = <<<EOT
<p><a href="$url/pages/{MENU_SLUG}" class="{MENU_CLASS}">{MENU_NAME}</a></p>

EOT;

//the HTML template for the login form
$login_template = <<<EOT
<h1>Login</h1>
<form action="" method="post">
<p><label for="password">Password???</label> <input type="password" name="password" /></p>
<p><input type="submit" name="login" value="Login" /></p>
</form>
EOT;

//the HTML template for the admin menu
$admin_menu_template = <<<EOT
<a href="?page=dash">Dashboard</a> - <a href="?page=posts">Posts</a> - <a href="?page=cats">Categories</a> - <a href="?page=pages">Pages</a> - <a href="?page=logout">Logout</a>
EOT;

//the HTML template for the dashboard
$dashboard_template = <<<EOT
<h1>Dashboard</h1>
<p>Welcome Óskar</p>
<p>You have used {SPACE_USED} of your allocated {SPACE_MAX}</p> 
EOT;

//the HTML template for the posts list
$post_list_template = <<<EOT
<ul class="post_list">
	<li>{POST_TITLE}</li>
    <li>{POST_DATE}</li>
    <li><a href="$url/cms/?page=posts&amp;action=update&amp;id={ID}">Update</a> - <a href="$url/cms/?page=posts&amp;action=delete&id={ID}">Delete</a></li>
</ul>
<div class="clear"></div>
EOT;

//the html template for updating posts
$post_update_template = <<<EOT
<form action="" method="post">
<input type="hidden" name="post[id]" value="{ID}" />
	<p><label for="title">Title</label> <input type="text" name="post[title]" value="{POST_TITLE}" /></p>
    <p><textarea name="post[content]">{POST_CONTENT}</textarea></p>
    <p><input type="submit" name="post[submit]" value="Update" />
</form>
EOT;

//the html template for deleting posts
$post_delete_template = <<<EOT
<h1>Delete {POST_TITLE}?</h1>
<form action="" method="post">
	<p><input type="submit" name="delete[yes]" value="Yes" />&nbsp;<input type="submit" name="delete[no]" value="No" /></p>
</form>	
EOT;

//the html template for catlist
$cat_list_template = <<<EOT
<ul class="post_list">
	<li>{CAT_NAME}</li>
	<li><a href="$url/cms/?page=cats&amp;action=update&amp;id={ID}">Update</a> - <a href="$url/cms/?page=cats&amp;action=delete&amp;id={ID}">Delete</a></li>
</ul>
<div class="clear"></div>
EOT;

//the html template for updating categories
$cat_update_template = <<<EOT
<form action="" method="post">
<input type="hidden" name="cat[id]" value="{ID}" />
<p><label for="name">Name:</label> <input type="text" name="cat[name]" value="{CAT_NAME}" /></p>
<p><label for="slug">Slug:</label> <input type="text" name="cat[slug]" value="{CAT_SLUG}" /><small>If no slug is specified, one will be derived from the title</small></p>
<p><input type="submit" name="cat[submit]" value="Update" /></p>
</form>
EOT;

//the html template for page list
$page_list_template = <<<EOT
<ul class="page_list">
<li>{PAGE_TITLE}</li>
<li>{DATE}</li>
<li>{CAT}</li>
<li><a href="$url/cms/?page=pages&amp;action=update&amp;id={ID}">Update</a> - <a href="$url/cms/?page=pages&amp;action=delete&amp;id={ID}">Delete</a></li>
</ul>
<div class="clear"></div>
EOT;

//the html template for updating pages
$page_update_template = <<<EOT
<form action="" method="post">
<input type="hidden" name="page[id]" value="{ID}" />
<p><label for="title">Title:</label> <input type="text" name="page[title]" value="{PAGE_TITLE}" /></p>
<p><label for="category">Category:</label>
<select name="page[cat]">
{CATEGORIES}
</select></p>
<p><label for="slug">Slug:</label> <input type="text" name="page[slug]" value="{PAGE_SLUG}" /></p>
<p><textarea name="page[content]">{PAGE_CONTENT}</textarea></p>
<p><input type="submit" name="page[update]" value="Update" /></p>
</form>
EOT;
?>