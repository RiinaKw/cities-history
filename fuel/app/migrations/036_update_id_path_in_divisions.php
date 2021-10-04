<?php

namespace Fuel\Migrations;

class Update_id_path_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find('all');
		if ($divisions) {
			foreach ($divisions as $division) {
				if ($division->parent_division_id === null) {
					$division->id_path = $division->id . '/';
					$division->save();
				} else {
					$parent = \Model_Division::find($division->parent_division_id);
					$division->id_path = $parent->id_path . $division->id . '/';
					$division->save();
				}
			}
		}
	}

	public function down()
	{
	}
}
