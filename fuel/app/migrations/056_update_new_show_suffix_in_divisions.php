<?php

namespace Fuel\Migrations;

class Update_new_show_suffix_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$division->new_show_suffix = $division->show_suffix;
				$division->save();
			}
		}
	}

	public function down()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$division->show_suffix = $division->new_show_suffix;
				$division->save();
			}
		}
	}
}
