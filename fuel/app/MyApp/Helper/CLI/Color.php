<?php

namespace MyApp\Helper\CLI;

use Exception;

/**
 * CLI の文字色・背景色を管理するクラス
 *
 * @package  App\Helper\CLI
 */
class Color
{

	/**
	 * CLI の文字色
	 * @var array<string, string>
	 */
	private const TEXT_COLORS = array(
		'black'        => '0;30',
		'dark_gray'    => '1;30',
		'blue'         => '0;34',
		'dark_blue'    => '1;34',
		'light_blue'   => '1;34',
		'green'        => '0;32',
		'light_green'  => '1;32',
		'cyan'         => '0;36',
		'light_cyan'   => '1;36',
		'red'          => '0;31',
		'light_red'    => '1;31',
		'purple'       => '0;35',
		'light_purple' => '1;35',
		'light_yellow' => '0;33',
		'yellow'       => '1;33',
		'light_gray'   => '0;37',
		'white'        => '1;37',
	);

	/**
	 * CLI の背景色
	 * @var array<string, string>
	 */
	private const BACKGROUND_COLORS = array(
		'black'      => '40',
		'red'        => '41',
		'green'      => '42',
		'yellow'     => '43',
		'blue'       => '44',
		'magenta'    => '45',
		'cyan'       => '46',
		'light_gray' => '47',
	);

	/**
	 * テキストに色を付ける
	 *
	 * @param string $text              テキスト
	 * @param string $text_color        文字色
	 * @param string $background_color  背景色
	 * @return string                   色付けされたテキスト
	 */
	public static function color(
		string $text,
		string $text_color = '',
		string $background_color = ''
	): string {
		$left = '';
		$right = '';

		if ($text_color && ! isset(self::TEXT_COLORS[$text_color])) {
			throw new Exception("unknown color : '{$text_color}'");
		}
		if ($background_color && ! isset(self::BACKGROUND_COLORS[$background_color])) {
			throw new Exception("unknown background color : '{$background_color}'");
		}

		$left = "\033[" . self::TEXT_COLORS[$text_color] . "m";
		if ($background_color) {
			$left .= "\033[" . self::BACKGROUND_COLORS[$background_color] . "m";
		}
		$right = "\033[0m";
		return $left . $text . $right;
	}
	// function color()

	/**
	 * テキストに「成功」を示す色をつける
	 * @param  string $text  テキスト
	 * @return string        色付けされたテキスト
	 */
	public static function success(string $text): string
	{
		return static::color($text, 'green');
	}

	/**
	 * テキストに「失敗」を示す色をつける
	 * @param  string $text  テキスト
	 * @return string        色付けされたテキスト
	 */
	public static function failure(string $text): string
	{
		return static::color($text, 'red');
	}
}
