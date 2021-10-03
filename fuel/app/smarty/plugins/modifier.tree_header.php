<?php

use MyApp\Model\Division\Tree;

function smarty_modifier_tree_header(Tree $tree, int $indentWidth = 0, string $indentType = 'tab'): string
{
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
