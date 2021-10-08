<?php

namespace MyApp\Helper;

/**
 * 文字列操作に関するヘルパークラス
 *
 * @package  App\Helper
 */
class Str
{
	/**
	 * ひらがなに変換
	 * @param  string $str  元の文字列
	 * @return string       ひらがなに変換された文字列
	 */
	public static function convertKana(string $str): string
	{
		return mb_convert_kana($str, "HVc");
	}
	// function convertKana()

	/**
	 * HTM 文字列から文章を切り出す
	 * @param  string  $source  元の HTML
	 * @param  integer $length  切り出す最大文字数
	 * @return string           切り出された文章
	 */
	public static function excerpt(string $source, int $length = 100): string
	{
		$source = preg_replace('/<script\s.*?>.*?<\/script>/', '', $source);
		$source = strip_tags($source);
		$source = preg_replace("/\s+/", ' ', $source);

		$source_length = mb_strlen($source);

		if ($source_length >= $length - 1) {
			return mb_substr($source, 0, $length) . '…';
		} else {
			return $source;
		}
	}
	// function excerpt()

	/**
	 * Wiki 構文を HTML に変換
	 * @param  string $source  Wiki 構文の文字列
	 * @return string          変換された HTML 文字列
	 */
	public static function wiki(string $source): string
	{
		$arrSource = [
			'[cite]',
			'[/cite]',
		];
		$arrDest = [
			'<cite>',
			'</cite>',
		];
		$content = str_replace($arrSource, $arrDest, $source);

		//$content = nl2br($content);

		if (strpos($content, '*') !== false) {
			$content = preg_replace('/^\*\s*(.*?)($|<br\s+\/>)/m', '<li>$1</li>', $content);
			$content = '<ul>' . PHP_EOL . $content . PHP_EOL . '</ul>';
		}

		// link tag
		preg_match_all("/\[\[(?<expressoin>.*?)\]\]/", $content, $matches);
		if ($matches) {
			foreach ($matches[0] as $key => $base) {
				$expression = $matches['expressoin'][$key];
				$arr = explode('|', $expression);

				$url = array_shift($arr);
				$text = array_shift($arr);

				$attrs = [
					'href' => $url,
				];
				foreach ($arr as $item) {
					list($name, $value) = explode(':', $item, 2);
					$attrs[$name] = $value;
				}

				$html_attrs = [];
				foreach ($attrs as $name => $value) {
					$html_attrs[] = sprintf(
						'%s="%s"',
						trim($name),
						trim($value)
					);
				}
				$html = '<a ' . implode(' ', $html_attrs) . '>' . trim($text) . '</a>';

				$content = str_replace($base, $html, $content);
			}
		}
		return $content;
	}
	// function wiki()
}
// class Helper_String
