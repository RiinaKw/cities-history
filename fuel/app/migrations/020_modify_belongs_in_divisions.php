<?php

namespace Fuel\Migrations;

class Modify_belongs_in_divisions
{
	public function up()
	{
		if (\DBUtil::field_exists('divisions', array('belongs_division_id')))
		{
			\DBUtil::modify_fields('divisions', array(
				'belongs_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			));
		}
	}

	public function down()
	{
	}
}
