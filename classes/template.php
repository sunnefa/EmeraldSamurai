<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 04.04.2011

/* This is classes/template.php */

class Template {
	private $output;
	private $small;
	
	public function html_master($template =  'theme/index.html') {
		$this->output = (file_exists($template)) ? file_get_contents($template) : die('Template Error!!!');	
	}
	
	public function html_small($template) {
		$this->small = $template;	
	}
	
	public function replace_tokens($tokens, $master = false) {
		foreach($tokens as $token => $data) {
			if($master) {
				$this->output = str_replace('{' . $token . '}', $data, $this->output);	
			}
			else {
				$this->small = str_replace('{' . $token . '}', $data, $this->small);	
			}
		}
	}
	
	public function display($master = false) {
		if($master) return $this->output;
		else return $this->small;
	}
	
	public function load_menu() {
		ob_start();
		global $menu_template;
		$queries = url_parser();
		$database = new Database();
		$menu_items = $database->get_data("SELECT p.title, p.slug FROM pages AS p");
		if(!isset($queries[1])) {
			echo "<a href=\"" . URL . "\" class=\"current\">Home</a>";	
		} else {
			echo "<a href=\"" . URL . "\">Home</a>";	
		}
		foreach($menu_items as $item) {
			$class = (isset($queries[1]) && $queries[1] == $item['slug']) ? 'current' : 'none';
			$this->html_small($menu_template);
			$this->replace_tokens(array('MENU_SLUG' => $item['slug'], 'MENU_NAME' => $item['title'], 'MENU_CLASS' => $class));
			echo $this->display();
		}
		return ob_get_clean();	
	}
}
?>