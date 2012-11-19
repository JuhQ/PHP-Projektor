<?php

class clLayout {
	
	/**
	 * Fetch the layout, insert class inside of content
	 * @param object $class
	 * @param string $title
	 * @param string $template
	 * @return boolean
	 */
	public static function view($class, $title = false, $template = false) {
		
		// if ajax call, don't print layout
		if (clFunctions::isAjax())
		{
			$class->view();
			return true;
		}
		
		if ($title === false)
		{
			$title = clConfig::get("default_title");
		}
		
		$arr = array();
		// define site title
		$arr['title'] = $title;
		
		// requested page fragment
		$arr['content'] = clTemplates::get($template, array("content" => $class));
		
		// main page which will include the page fragmen
		echo clTemplates::get(clConfig::get("default_template"), $arr);
		
		return true;
	}
}