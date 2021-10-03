<?php

use  MyApp\Helper\Iterator;

function smarty_modifier_tree_body(Iterator $iterator, int $indentWidth = 0, string $indentType = 'tab'): string
{
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
	$smarty->iterator = $iterator;
	$smarty->indentType = $indentType;
	$html = rtrim((string)$smarty);
	return str_replace("\n", "\n" . $indent, $html);
}
