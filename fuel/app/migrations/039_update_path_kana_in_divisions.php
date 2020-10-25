<?php

namespace Fuel\Migrations;

class Update_path_kana_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$division->fullname = $division->get_fullname();
				$division->fullname_kana = $division->get_fullname_kana();
				$division->path = $division->make_path();
				$division->path_kana = $division->make_path_kana();
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
