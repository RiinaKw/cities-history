<?php

/**
 * @package  App\Smarty
 */

use MyApp\Helper\Date;

/**
 * 日本語に対応した date_format
 * @param  string|int $input   日時の文字列あるいはタイムスタンプ
 * @param  string     $format  フォーマット形式
 * @return string              出力文字列
 */
function smarty_modifier_date_format2($input, string $format): string
{
	return Date::format($format, $input);
}
