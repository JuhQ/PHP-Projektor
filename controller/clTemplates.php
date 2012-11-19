<?php

class clTemplates {
	
	/**
	 * Get template from database
	 * @param strin $template
	 * @return string
	 */
	public static function getTemplate($template) {
		$file = clConfig::get("base") . "/view/templates/" . $template . ".html";
		if(!file_exists($file)) {
			return false;
		}	
		return file_get_contents($file);
	}
	
	/**
	 * Get template, parse content and insert into template
	 * @param string $template
	 * @param array $array
	 * @return string
	 */
	public static function get($template, $arr = false, $get = true) {
		
		// template can be string given to this function, or then fetched from database
		if ($get === true)
		{
			$template = self::getTemplate($template);
		}
		if($template === false) {
			return false;
		}
		
		if ($arr !== false)
		{
			foreach ($arr as $key => $value)
			{
				// if value is an object, look for view() method, otherwise handle as a string
				if (is_object($value))
				{
					ob_start();
					$value->view();
					$value = ob_get_contents();
					ob_end_clean();
				}
				
				$template = str_replace('{{' . $key . '}}', $value, $template);
			}
		}
		
		// awesome translation tool for templates, yay!
		$template = preg_replace_callback("/{{lang:(.*?)}}/", array("clLang", "get"), $template);
		
		// replace everything that was left empty
		$template = preg_replace("/{{(.*?)}}/", "", $template);
		
		return $template;
	}
}