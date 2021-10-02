<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Tasks;

/**
 * Robot example task
 *
 * Ruthlessly stolen from the beareded Canadian sexy symbol:
 *
 *		Derek Allard: http://derekallard.com/
 *
 * @package		Fuel
 * @version		1.0
 * @author		Phil Sturgeon
 */

function dump($division, $nest = 0)
{
	$indent = str_repeat(' ', $nest);
	echo $indent, $division->id_path, ' ', $division->path, PHP_EOL;
}

class Test_Division
{
	public static function run()
	{
		$root = \Model_Division::find_one_by_path('北海道');
		$tree = new \Model_Division_Tree($root);

		$divisions = \Model_Division::get_by_parent_division_and_date($root);
		foreach ($divisions as $division) {
			if ($division->suffix === '郡') {
				\Model_Division_Tree::dump_division($division);
			}
		}
		var_dump('--------');
		$tree->make_tree($divisions);

		$tree->dump();
	}

	public static function test()
	{
		$iterator = new MyIterator();
		$iterator->push(1, 'a');
		$iterator->push(2, 'b');
		$iterator->push(3, 'c');
		var_dump($iterator);

		foreach ($iterator as $item) {
			var_dump($item);
		}

		var_dump($iterator->get('b'));
	}
}
