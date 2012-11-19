<?php

/**
 * This class contains generic functions.
 * This is a static class, so all the methods need to be static
 */
class clFunctions {
	
	/**
	 * Redirect user to different url, no redirect if ajax call
	 * @param string $url
	 */
	public static function redirect($url) {
		if (self::isAjax())
		{
			return false;
		}
		header("Location: " . $url);
		exit();
	}
	
	/**
	 * Debug data
	 * @param mixed $data
	 */
	public static function prePrint($data) {
		echo '<pre>' . print_r($data, true) . '</pre>';
	}
	
	/**
	 * Check if ajax call was made
	 * @author Sean Koole - http://blog.seankoole.com/detecting-ajax-calls-php-example
	 * @return boolean
	 */
	public static function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	/**
	 * Shorten urls to reasonable length
	 * @param string $str
	 * @param int $len
	 * @return string
	 */
	public static function urlShortener($str, $len = 80) {
		if (strlen($str) >= $len)
		{
			$str = substr($str, 0, 20) . '...' . substr($str, -20);
		}
		return $str;
	}
	
	/**
	 * Create urls to links
	 * @param string $str
	 * @return string
	 */
	public static function str2link($str) {
		$from = "/(?<!<a href=\")((http|ftp)+(s)?:\\/\\/[^<>\\s]+)/e";
		$to = "'<a href=\"\\0\" title=\"\\0\">' . self::urlShortener('\\0') . '</a>'";
		
		return preg_replace($from, $to, $str);
	}
	
	/**
	 * Convert strings to UTF-8 and encode html
	 * @param string $str
	 * @return string
	 */
	public static function utf8entities($str) {
		$str = self::utf8(stripslashes((string) $str));
		return htmlentities($str, ENT_COMPAT, "UTF-8");
	}
	
	/**
	 * Convert strings to UTF-8
	 * @param string $str
	 * @return string
	 */
	public static function utf8($str) {
		return mb_convert_encoding((string) $str, "UTF-8", "auto");
	}
	
	/**
	 * Return time correctly formatted
	 * @return string
	 */
	public static function date($time = null, $format = "d.m.Y H:i") {
		date_default_timezone_set(clConfig::get('default_timezone'));
		if ($time !== false && !is_numeric($time))
		{
			$time = strtotime($time);
		}
		
		return date($format, $time);
	}
	
	/**
	 * Validate an email address.
	 * Provide email address (raw input)
	 * Returns true if the email address has the email
	 * address format and the domain exists.
	 * @return boolean
	 */
	public static function validEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex + 1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64)
			{
				// local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255)
			{
				// domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen - 1] == '.')
			{
				// local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local))
			{
				// local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
				// character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain))
			{
				// domain part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local)))
			{
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
				{
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A")))
			{
				// domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
	
	/**
	 * Create clean urls
	 * @param string $url
	 * @return string
	 */
	public static function cleanUrl($url) {
		$url = self::utf8($url);
		$url = trim(strtolower(stripslashes($url)));
		$url = preg_replace("/\\s+/", "-", $url);
		$url = preg_replace("/[^a-z0-9-_]/", "", $url);
		
		// finally remove extra dashes
		$url = trim($url, '-');
		
		return $url;
	}
	
	/**
	 * Create placeholder image
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public static function placeholder($width = 100, $height = 100) {
		return '<img src="http://placehold.it/' . $width . 'x' . $height . '" alt="placeholder" title="placeholder" />';
	}
	
	/**
	 * Calculate exif information to number
	 * @param int $value
	 * @param string $format
	 * @return float
	 */
	public static function exifToNumber($value, $format) {
		$spos = strpos($value, '/');
		if ($spos === false)
		{
			return sprintf($format, $value);
		}
		else
		{
			list($base, $divider) = split("/", $value, 2);
			if ($divider == 0)
			{
				return sprintf($format, 0);
			}
			else
			{
				return sprintf($format, ($base / $divider));
			}
		}
	}
	
	/**
	 * Convert exif information to GPS coordinate
	 * @param string $reference
	 * @param float $coordinate
	 * @return float
	 */
	public static function exifToCoordinate($reference, $coordinate) {
		$prefix = '';
		if ($reference == 'S' || $reference == 'W')
		{
			$prefix = '-';
		}
		return $prefix . sprintf('%.6F', self::exifToNumber($coordinate[0], '%.6F') + (((self::exifToNumber($coordinate[1], '%.6F') * 60) + (self::exifToNumber($coordinate[2], '%.6F'))) / 3600));
	}
	
	/**
	 * Get GPS coordinates from image
	 * @param string $filename
	 * @return array
	 */
	public static function getCoordinates($filename) {
		if (extension_loaded('exif'))
		{
			$exif = exif_read_data($filename, 'EXIF');
			
			if (isset($exif['GPSLatitudeRef']) && isset($exif['GPSLatitude']) && isset($exif['GPSLongitudeRef']) && isset($exif['GPSLongitude']))
			{
				return array(self::exifToCoordinate($exif['GPSLatitudeRef'], $exif['GPSLatitude']), self::exifToCoordinate($exif['GPSLongitudeRef'], $exif['GPSLongitude']));
			}
		}
		return false;
	}
}