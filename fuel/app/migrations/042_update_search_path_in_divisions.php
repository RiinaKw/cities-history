<?php

namespace Fuel\Migrations;

class Update_search_path_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::query();
		if ($divisions) {
			foreach ($divisions as $division) {
				$getter = $division->getter();
				$division->search_path = $getter->search_path;
				$division->search_path_kana = $getter->search_path_kana;
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
