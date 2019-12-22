<?php

namespace Fuel\Migrations;

class Update_identifier_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$division->identifier = $division->identify ?: null;
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
				$division->identify = $division->identifier;
				$division->save();
			}
		}
	}
}
