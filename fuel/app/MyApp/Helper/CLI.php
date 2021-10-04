<?php

/**
 * @package  App\Helper\CLI
 */

namespace MyApp\Helper;

class CLI
{
	/**
	 * ユーザからの入力を待つ
	 * @return string  入力された値
	 */
	public static function prompt(): string
	{
		$string = trim(fgets(STDIN, 64));
		return $string;
	}

	/**
	 * ポスワードなど画面に表示しないプロンプト
	 * @return string  入力された値
	 */
	public static function promptHidden(): string
	{
		if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
			// for windows
			//flock(STDIN, LOCK_EX);
			//echo "\033[8m";
			$string = trim(fgets(STDIN, 64));
			//echo "\033[0m";
			//flock(STDIN, LOCK_UN);
		} else {
			// for linux
			//system('stty -echo'); // エコーバックをOFFにする
			//flock(STDIN, LOCK_EX);
			//echo "\033[8m";
			$string = trim(fgets(STDIN, 64));
			//echo "\033[0m";
			//flock(STDIN, LOCK_UN);
			//system('stty echo'); // エコーバックをONに戻す
		}
		return $string;
	}
}
