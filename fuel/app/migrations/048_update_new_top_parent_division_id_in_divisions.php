<?php

namespace Fuel\Migrations;

class Update_new_top_parent_division_id_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				if ( isset($division->belongs_division_id) )
				{
					$division->new_belongs_division_id = $division->belongs_division_id;
				}
				if ( isset($division->top_parent_division_id) )
				{
					$division->new_belongs_division_id = $division->top_parent_division_id;
				}
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
				$division->belongs_division_id = $division->new_belongs_division_id;
				$division->top_parent_division_id = $division->new_top_parent_division_id;
				$division->save();
			}
		}
	}
}
