<?php

class clConfig {
	
	/**
	 * Config data is stored here
	 * @var array
	 */
	private static $data = array();
	
	/**
	 * Load configuration file
	 * @param string $base
	 * @todo file validation
	 */
	public static function load($base) {
		$data = parse_ini_file($base . "/config.ini", true);
		self::$data = $data[$data['config']];
		self::$data['base'] = $base;
	}
	
	/**
	 * Get specific configuration value
	 * @param string $key
	 * @return mixed array / string
	 */
	public static function get($key) {
		return self::$data[$key];
	}
}