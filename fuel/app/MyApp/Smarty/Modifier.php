<?php

/**
 * @package  App\Smarty
 */

namespace MyApp\Smarty;

use View_Smarty;
use Model_Division;
use MyApp\Helper\Date;
use MyApp\Helper\Iterator;
use MyApp\Model\Division\Tree;

class Modifier
{
	/**
	 * プラグイン関数を Smarty に登録する
	 */
	public static function init(): void
	{
		$smarty = View_Smarty::parser();
		$class = static::class;

		foreach (get_class_methods(static::class) as $name) {
			if ($name !== 'init') {
				$smarty->registerPlugin('modifier', $name, "{$class}::{$name}");
			}
		}
	}

	/**
	 * 日本語に対応した date_format
	 * @param  string|int $input   日時の文字列あるいはタイムスタンプ
	 * @param  string     $format  フォーマット形式
	 * @return string              出力文字列
	 */
	public static function date_format2($input, string $format): string
	{
		return Date::format($format, $input);
	}

	/**
	 * 自治体のヘッダー（h4）タグを出力
	 * @param  Model_Division $division     自治体オブジェクト
	 * @param  integer        $indentWidth  インデント幅
	 * @param  string         $indentType   インデントタイプ( sp or tab)
	 * @return string                       出力 HTML
	 */
	public static function division_h4(
		Model_Division $division,
		int $indentWidth = 0,
		string $indentType = 'tab'
	): string {
		$char = '';
		$width = 1;
		switch ($indentType) {
			case 'tab':
				$char = "\t";
				break;

			case 'sp':
				$char = ' ';
				$width = 4;
				break;
		}
		$indent = str_repeat($char, $indentWidth * $width);

		$smarty = View_Smarty::forge('components/smarty_plugins/division_h4.tpl');
		$smarty->division = $division;
		$smarty->indentType = $indentType;
		$html = rtrim((string)$smarty);
		return str_replace("\n", "\n" . $indent, $html);
	}


	/**
	 * 自治体一覧を出力
	 * @param  MyApp\Helper\Iterator $iterator     自治体リスト
	 * @param  integer               $indentWidth  インデント幅
	 * @param  string                $indentType   インデントタイプ( sp or tab)
	 * @return string  出力 HTML
	 */

	public static function tree_body(
		?Iterator $iterator,
		int $indentWidth = 0,
		string $indentType = 'tab'
	): string {
		if (! $iterator) {
			return '';
		}
		$char = '';
		switch ($indentType) {
			case 'tab':
				$char = "\t";
				break;

			case 'sp':
				$char = ' ';
				break;
		}
		$indent = str_repeat($char, $indentWidth);

		$smarty = View_Smarty::forge('components/smarty_plugins/tree_body.tpl');
		$smarty->iterator = $iterator->array();
		$smarty->indentType = $indentType;
		$html = rtrim((string)$smarty);
		return str_replace("\n", "\n" . $indent, $html);
	}

	/**
	 * 自治体ツリーのヘッダを出力
	 *
	 * @param  MyApp\Model\Division\Tree $tree         自治体ツリー
	 * @param  integer                   $indentWidth  インデント幅
	 * @param  string                    $indentType   インデントタイプ( sp or tab)
	 * @return string  出力 HTML
	 */
	public static function tree_header(
		Tree $tree,
		int $indentWidth = 0,
		string $indentType = 'tab'
	): string {
		$char = '';
		$width = 1;
		switch ($indentType) {
			case 'tab':
				$char = "\t";
				break;

			case 'sp':
				$char = ' ';
				$width = 4;
				break;
		}
		$indent = str_repeat($char, $indentWidth * $width);

		$smarty = View_Smarty::forge('components/smarty_plugins/tree_header.tpl');
		$smarty->tree = $tree;
		$smarty->indentType = $indentType;
		$html = rtrim((string)$smarty);
		return str_replace("\n", "\n" . $indent, $html);
	}
}
