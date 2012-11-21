<?php
class uiModule {
	
	/**
	 * Page view object
	 * @var object
	 */
	public $object = false;
	
	/**
	 * Page title
	 * @var string
	 */
	public $title = false;
	
	/**
	 * Template name
	 * @var string
	 */
	public $template = false;
	
	/**
	 * Return the layout
	 * @return boolean
	 */
	public function view() {
		// if no view object is defined, create one dynamically
		if ($this->object === false)
		{
			$this->object = "ui" . ucfirst(get_class($this));
		}
		
		// if no template is defined, create one dynamically
		if ($this->template === false)
		{
			$this->template = get_class($this);
		}
		
		$object = false;
		if(class_exists($this->object)) {
			$object = new $this->object();
		}

		return clLayout::view($object, $this->title, $this->template);
	}
}