<?php

/**
 * @package  App\Smarty
 */

use MyApp\Model\Division\Tree;

/**
 * 自治体ツリーのヘッダを出力
 *
 * @param  MyApp\Model\Division\Tree $tree         自治体ツリー
 * @param  integer                   $indentWidth  インデント幅
 * @param  string                    $indentType   インデントタイプ( sp or tab)
 * @return string  出力 HTML
 */
function smarty_modifier_tree_header(
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
