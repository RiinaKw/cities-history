<?php

namespace Fuel\Migrations;

class Update_fullname_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions) {
			foreach ($divisions as $division) {
				$division->fullname = $division->get_path(null, true);
				$division->fullname_kana = $division->name_kana . $division->postfix_kana;
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
