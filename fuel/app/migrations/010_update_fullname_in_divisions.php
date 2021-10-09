<?php

namespace Fuel\Migrations;

class Update_fullname_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find('all');
		if ($divisions) {
			foreach ($divisions as $division) {
				$division->fullname = basename($division->path);
				$division->fullname_kana = $division->name_kana . $division->postfix_kana;
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
