<?php

class clRouter {
	
	/**
	 * Class name is stored into this class
	 * @var string
	 */
	private $class = false;
	
	/**
	 * Get the proper class used
	 * @return mixed string / boolean
	 */
	public function getClass() {
		return $this->class;
	}
	
	/**
	 * Set data to the router
	 * @param array $arr
	 * @return boolean
	 */
	public function setData($arr) {
		$this->class = $arr[0];
		$getArray = array();
		if (count($arr) == 2)
		{
			$getArray[$arr[1]] = "";
			$_GET = $getArray;
			return true;
		}
		foreach ($arr as $key => $value)
		{
			// first one is class, so skip that
			if ($key == 0)
			{
				continue;
			}
			if ($key % 2 == 1)
			{
				if (isset($arr[$key + 1]))
				{
					$getArray[$value] = $arr[$key + 1];
				}
			}
		}
		
		$_GET = $getArray;
		return true;
	}
}