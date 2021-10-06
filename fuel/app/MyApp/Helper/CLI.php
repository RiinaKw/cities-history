<?php

namespace MyApp\Helper;

/**
 * CLI 関連のヘルパークラス
 * @package  App\Helper\CLI
 */
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
	 * パスワードなど画面に表示しないプロンプト
	 * @return string  入力された値
	 *
	 * @todo 入力値が見えちゃうんだけど、なんで？？？
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
