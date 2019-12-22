<?php

namespace Fuel\Migrations;

class Update_new_fullname_and_new_fullname_kana_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				$name = $division->name;
				if ($division->show_suffix)
				{
					$name .= $division->suffix;
				}
				if ($division->identifier)
				{
					$name .= '('.$division->identifier.')';
				}

				$division->new_fullname = $name;
				$division->new_fullname_kana = $division->fullname_kana;
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
				$division->fullname = $division->new_fullname;
				$division->fullname_kana = $division->new_fullname_kana;
				$division->save();
			}
		}
	}
}
