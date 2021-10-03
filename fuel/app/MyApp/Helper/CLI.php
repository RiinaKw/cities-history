<?php

/**
 * @package  App\Helper\CLI
 */

namespace MyApp\Helper;

class CLI
{
	/**
	 * 標準入力
	 * @var string
	 */
	private const STDIN = 'php://stdin';

	/**
	 * ユーザからの入力を待つ
	 * @return string  入力された値
	 */
	public static function prompt(): string
	{
		$stdin = fopen(static::STDIN, 'r');
		$string = trim(fgets($stdin, 64));
		fclose($stdin);
		return $string;
	}
}
