<?php

class Helper_Input
{

	public static function ip()
	{
		$target = ['HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
		$ip = '0.0.0.0';
		foreach ($target as $key) {
			if (isset($_SERVER[$key])) {
				return $_SERVER[$key];
			}
		}
	}
	// function ip()
}
// class Helper_Input
