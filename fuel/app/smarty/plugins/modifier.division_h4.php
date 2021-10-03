<?php

/**
 * @package  App\Smarty
 */

/**
 * 自治体のヘッダー（h4）タグを出力
 * @param  Model_Division $division     自治体オブジェクト
 * @param  integer        $indentWidth  インデント幅
 * @param  string         $indentType   インデントタイプ( sp or tab)
 * @return string                       出力 HTML
 */
function smarty_modifier_division_h4(
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
