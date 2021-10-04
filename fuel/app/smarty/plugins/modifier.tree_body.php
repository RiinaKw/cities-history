<?php

/**
 * @package  App\Smarty
 */

use  MyApp\Helper\Iterator;

/**
 * 自治体一覧を出力
 * @param  MyApp\Helper\Iterator $iterator     自治体リスト
 * @param  integer               $indentWidth  インデント幅
 * @param  string                $indentType   インデントタイプ( sp or tab)
 * @return string  出力 HTML
 */

function smarty_modifier_tree_body(
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
