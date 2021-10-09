<?php

namespace Fuel\Migrations;

class Update_source_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::query();
		if ($divisions) {
			foreach ($divisions as $division) {
				$division->source = 'Wikipedia';
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
