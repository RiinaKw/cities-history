<?php

namespace Fuel\Migrations;

class Delete_top_parent_division_id_from_divisions
{
	public function up()
	{
		if (\DBUtil::field_exists('divisions', array('top_parent_division_id')))
		{
			\DBUtil::drop_fields('divisions', array(
				'top_parent_division_id',
			));
		}
	}

	public function down()
	{
		if ( ! \DBUtil::field_exists('divisions', array('top_parent_division_id')))
		{
			\DBUtil::add_fields('divisions', array(
				'top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			));
		}
		\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');

		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
				if ($division->parent_division_id)
				{
					$parent = $division;
					while ($parent->parent_division_id !== null)
					{
						$parent = \Model_Division::find_by_pk($parent->parent_division_id);
					}
					$division->top_parent_division_id = $parent->id;
					$division->save();
				}
			}
		}
	}
}
