<?php

namespace Fuel\Migrations;

class Delete_identify_from_divisions
{
	public function up()
	{
		\DBUtil::drop_fields('divisions', array(
			'identify'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('divisions', array(
			'identify' => array('constraint' => 50,  'null' => true, 'type' => 'varchar'),
		));
	}
}
