<?php

class Helper_Input
{
	/**
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function ip()
	{
		$target = ['HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
		foreach ($target as $key) {
			if (isset($_SERVER[$key])) {
				return $_SERVER[$key];
			}
		}
	}
	// function ip()
}
// class Helper_Input
