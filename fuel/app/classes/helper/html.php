<?php

class Helper_Html
{
	public static function excerpt($source, $length = 100)
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
	} // function excerpt()

	public static function wiki($source)
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
	} // function wiki()
} // class Helper_Html
