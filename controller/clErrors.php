<?php
class clErrors {
	
	/**
	 * Show error 404
	 */
	public static function error404() {
		clLayout::view(new uiError404());
	}
}