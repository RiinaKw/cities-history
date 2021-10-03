<?php

/**
 * @package  App\Task
 */

namespace Fuel\Tasks;

use Model_Division;
use MyApp\Model\Division\Tree;
use MyApp\Table\Division as DivisionTable;
use MyApp\Helper\Iterator;

class Test_Division
{
	public static function run()
	{
		$root = Model_Division::find_one_by_path('北海道');
		$tree = new Tree($root);

		$divisions = DivisionTable::get_by_parent_division_and_date($root);
		$tree->make_tree($divisions)->dump();
	}

	public static function test()
	{
		$iterator = new Iterator();
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
