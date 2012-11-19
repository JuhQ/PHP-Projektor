<?php

class clLang {
	
	/**
	 * Translation is stored in this variable
	 * @var array
	 */
	private static $text = false;
	
	/**
	 * Load configuration
	 * @return array
	 */
	private static function load() {
		if (self::$text !== false)
		{
			return self::$text;
		}
		$language = clConfig::get("language");
		$file = clConfig::get("localization_directory") . "/" . $language . ".ini";
		if (!file_exists($file))
		{
			return false;
		}
		
		self::$text = parse_ini_file($file, true);
		return self::$text;
	}
	
	/**
	 * Get specific translation
	 * @param string $const
	 * @return strinf
	 */
	public static function get($const) {
		if (self::$text === false)
		{
			self::load();
		}
		
		return self::$text[$const];
	}
}