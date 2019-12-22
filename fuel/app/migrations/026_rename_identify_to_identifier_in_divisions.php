<?php

namespace Fuel\Migrations;

class Rename_identify_to_identifier_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'identify' => array('constraint' => 200,  'null' => true, 'type' => 'varchar', 'name' => 'identifier'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('divisions', array(
			'identifier' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'identify'),
		));
	}
}
