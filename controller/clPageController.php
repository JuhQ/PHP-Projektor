<?php

class clPageController {
	
	/**
	 * Basefolder for the project
	 * @var unknown_type
	 */
	private $baseFolder;
	
	/**
	 * Naming convention follows specific pattern, this array will help the autoloader to find correct folder for classes
	 * @var array
	 */
	private $folders = array("cl" => "controller", "db" => "model", "ui" => "view/classes", "" => "view/pages");
	
	/**
	 * Initialize sessions, output buffers, register autoloader and other basic setups
	 */
	public function __construct() {
		session_start();
		ob_start();
		
		$this->baseFolder = dirname(__DIR__);
		
		spl_autoload_register(array($this, "autoload"));
		clConfig::load($this->baseFolder);
		
		header('Content-type: text/html; charset=' . clConfig::get('charset'));
		date_default_timezone_set(clConfig::get('default_timezone'));
		
		// show errors in development, hide them in production
		if (clConfig::get("config") === "development")
		{
			error_reporting(E_ALL);
		}
		else
		{
			error_reporting(0);
		}
		
		$this->pageController();
	}
	
	/**
	 * Autoloader, will look files in specific directories
	 * @param string $class
	 * @param string $extension
	 * @return boolean
	 */
	private function autoload($class, $extension = "php") {
		if ($this->folders[substr($class, 0, 2)])
		{
			$file = $this->baseFolder . "/" . $this->folders[substr($class, 0, 2)] . "/" . $class . "." . $extension;
			if ($this->load($file))
			{
				return true;
			}
		}
		foreach ($this->folders as $short => $folder)
		{
			$file = $this->baseFolder . "/" . $folder . "/" . $class . "." . $extension;
			if ($this->load($file))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Include file, very basic stuff
	 * @param string $file
	 * @return boolean
	 */
	private function load($file) {
		if (file_exists($file))
		{
			include ($file);
			return true;
		}
		return false;
	}
	
	/**
	 * Quick and dirty hack to point urls to files
	 * @todo someone please make this better
	 */
	private function pageController() {
		$className = "index";
		if (isset($_GET['__page']) && !empty($_GET['__page']))
		{
			$className = $_GET['__page'];
		}
		
		unset($_GET['__page']);
		
		$className = str_replace(".php", "", $className);
		$arr = explode("/", $className);
		
		// check that size is bigger than one, because the className itself will always be the first one
		if (sizeof($arr) > 1)
		{
			$router = new clRouter();
			$router->setData($arr);
			$className = $router->getClass();
		}
		
		// if page file does not exist, show error 404 page
		if (!file_exists($this->baseFolder . '/view/pages/' . $className . '.php'))
		{
			clErrors::error404();
			exit();
		}
		
		// if the above doesn't validate and object still can't be shown, show the freaking error 404
		if (!$this->autoload($className))
		{
			clErrors::error404();
		}
		else
		{
			$page = new $className();
			$page->view();
		}
		
		$content = ob_get_contents();
		ob_clean();
		echo $content;
	}
}