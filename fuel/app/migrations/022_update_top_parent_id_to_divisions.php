<?php

namespace Fuel\Migrations;

class Update_top_parent_id_to_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find('all');
		if ($divisions) {
			foreach ($divisions as $division) {
				if ($division->parent_division_id) {
					$parent = $division;
					while ($parent->parent_division_id !== null) {
						$parent = \Model_Division::find($parent->parent_division_id);
					}
					$division->top_parent_division_id = $parent->id;
					$division->save();
				}
			}
		}
	}

	public function down()
	{
	}
}
