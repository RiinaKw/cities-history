<?php

namespace MyApp\Helper;

/**
 * 数値を文字列に変換するヘルパークラス
 *
 * @package  App\Helper
 */
class Number
{
	public static function format($number, $increment, array $units = [], int $digits = 2)
	{
		$idx = 0;
		while ($number >= $increment) {
			$number /= $increment;
			++$idx;
		}
		$number = round($number, $digits);
		return $number . ' ' . $units[$idx];
	}
}
