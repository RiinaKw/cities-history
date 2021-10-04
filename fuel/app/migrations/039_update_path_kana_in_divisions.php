<?php

namespace Fuel\Migrations;

class Update_path_kana_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find('all');
		if ($divisions) {
			foreach ($divisions as $division) {
				$getter = $division->getter();
				$division->fullname = $getter->fullname;
				$division->fullname_kana = $getter->fullname_kana;
				$division->path = $getter->path;
				$division->path_kana = $getter->path_kana;
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
