<?php

namespace MyApp\Helper;

/**
 * 和暦に対応した日時フォーマットクラス
 * @package  App\Helper
 */
class Date
{
	/**
	 * 元号用設定
	 *
	 * 参考 : http://ja.wikipedia.org/wiki/%E5%85%83%E5%8F%B7%E4%B8%80%E8%A6%A7_%28%E6%97%A5%E6%9C%AC%29
	 * @var array<int, array<string, int|string>>
	 */
	private static $gengoList = [
		['name' => '令和',   'name_short' => 'R',  'timestamp' =>   1556636400], // 2019-05-01,
		['name' => '平成',   'name_short' => 'H',  'timestamp' =>    600188400], // 1989-01-08,
		['name' => '昭和',   'name_short' => 'S',  'timestamp' =>  -1357635600], // 1926-12-25'
		['name' => '大正',   'name_short' => 'T',  'timestamp' =>  -1812186000], // 1912-07-30
		['name' => '明治',   'name_short' => 'M',  'timestamp' =>  -3216790800], // 1868-01-25
		['name' => '西暦',   'name_short' => 'AD', 'timestamp' => -62135630339],
		['name' => '紀元前', 'name_short' => 'BC', 'timestamp' => PHP_INT_MIN],
	];

	/**
	 * 日本語曜日設定
	 * @var array<int, string>
	 */
	private static $weekJp = [
		0 => '日',
		1 => '月',
		2 => '火',
		3 => '水',
		4 => '木',
		5 => '金',
		6 => '土',
	];

	/**
	 * 午前午後
	 * @var array<string, string>
	 */
	private static $ampm = [
		'am' => '午前',
		'pm' => '午後',
	];

	/**
	 * フォーマット内の文字を置換する
	 *
	 * @param  string    $format    フォーマット文字列
	 * @param  string    $char      置換対象の文字
	 * @param  callable  $callback  置換すべき値
	 * @return string  変換後のフォーマット文字列
	 */
	private static function replace(string $format, string $char, callable $callback): string
	{
		if (strpos($format, $char) !== false) {
			$re = '/' . $char . '/';
			$format = preg_replace($re, $callback(), $format);
		}
		return $format;
	}

	private static function getGengo(int $timestamp)
	{
		foreach (static::$gengoList as $g) {
			if ($g['timestamp'] <= $timestamp) {
				return $g;
			}
		}
		// 元号が取得できない場合はException
		throw new \Exception('Cannot be converted to gengo : ' . $timestamp);
	}

	/**
	 * タイムスタンプを取得
	 *
	 * @param  int|string|null  $timestamp  タイムスタンプもしくは日時を示す文字列、null の場合は現在時刻
	 * @return int  正規化されたタイムスタンプ
	 */
	private static function getTimestamp($timestamp = null): ?int
	{
		if (is_null($timestamp)) {
			return time();
		}
		if (is_string($timestamp)) {
			return static::forTimestamp($timestamp);
		}
		return $timestamp;
	}

	protected static function forTimestamp(string $formatted): ?int
	{
		$hour = 0;
		$min = 0;
		$sec = 0;
		if (preg_match('/(?<hour>\d{1,2}):(?<min>\d{1,2})(:(?<sec>\d{1,2}))?$/', $formatted, $matches)) {
			$hour = (int)$matches['hour'];
			$min = (int)$matches['min'];
			$sec = (int)($matches['sec'] ?? 0);
		}

		if (preg_match('/^(?<year>\d{1,4})-(?<month>\d{1,2})-(?<day>\d{1,2})/', $formatted)) {
			$date = new \DateTime($formatted);
			return $date->format('U');
		} else if (preg_match('/^(?<gengo>[A-Z]{1,2})(?<year>\d{1,2})-(?<month>\d{1,2})-(?<day>\d{1,2})/', $formatted, $matches)) {
			$gengo = null;
			foreach (static::$gengoList as $cur) {
				if ($matches['gengo'] == $cur['name_short']) {
					$gengo = $cur;
					break;
				}
			}
			if (! $gengo) {
				throw new \Exception('Unknown gengo : ' . $matches['gengo']);
			}
			$start_year = (int)date('Y', $gengo['timestamp']);
			$year = $start_year + (int)$matches['year'] - 1;
			return mktime($hour, $min, $sec, $matches['month'], $matches['day'], $year);
		} else {
			return null;
		}
	}

	/**
	 * 和暦などを追加したdate関数
	 *
	 * 追加した記号
	 * J : 元号
	 * b : 元号略称
	 * K : 和暦年(1年を元年と表記)
	 * k : 和暦年
	 * x : 日本語曜日(0:日-6:土)
	 * E : 午前午後
	 *
	 * @param  string           $format     フォーマット文字列
	 * @param  int|string|null  $timestamp  タイムスタンプもしくは日時を示す文字列、null の場合は現在時刻
	 * @return string  フォーマット済みの日時文字列
	 */
	public static function format(string $format, $timestamp = null): string
	{
		// 和暦関連のオプションがある場合は和暦取得
		$gengo = [];
		$timestamp = static::getTimestamp($timestamp);
		if (preg_match('/[J|b|K|k]/', $format)) {
			$gengo = static::getGengo($timestamp);
		}

		// J : 元号
		$format = static::replace($format, 'J', function () use ($gengo) {
			return $gengo['name'];
		});

		// b : 元号略称
		$format = static::replace($format, 'b', function () use ($gengo) {
			'\\' . $gengo['name_short'];
		});

		// K : 和暦用年(元年表示)
		$format = static::replace($format, 'K', function () use ($gengo, $timestamp) {
			if ($gengo['name_short'] == 'BC') {
				return abs(date('Y', $timestamp));
			}
			$year = date('Y', $timestamp) - date('Y', $gengo['timestamp']) + 1;
			return $year == 1 ? '元' : $year;
		});

		// k : 和暦用年
		$format = static::replace($format, 'k', function () use ($gengo, $timestamp) {
			if ($gengo['name_short'] == 'BC') {
				return abs(date('Y', $timestamp));
			}
			return (int)date('Y', $timestamp) - (int)date('Y', (int)$gengo['timestamp']) + 1;
		});

		// x : 日本語曜日
		$format = static::replace($format, 'x', function () use ($timestamp) {
			$w = date('w', $timestamp);
			return static::$weekJp[$w];
		});

		// E : 午前午後
		$format = static::replace($format, 'E', function () use ($timestamp) {
			$a = date('a', $timestamp);
			return static::$ampm[$a];
		});

		// p : 時。12時間単位。先頭にゼロを付けない。(0-11)
		$format = static::replace($format, 'p', function () use ($timestamp) {
			$hour = date('g', $timestamp);
			return $hour == 12 ? 0 : $hour;
		});

		// q : 時。数字。12 時間単位。(00-11)
		$format = static::replace($format, 'q', function () use ($timestamp) {
			$hour = date('h', $timestamp);
			return str_pad($hour == 12 ? 0 : $hour, 2, '0');
		});

		return date($format, $timestamp);
	}
	// function format()
}
// Helper_Date
