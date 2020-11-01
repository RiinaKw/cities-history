<?php

namespace Fuel\Migrations;

class Update_search_path_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$division->search_path = $division->make_search_path();
				$division->search_path_kana = $division->make_search_path_kana();
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
