<?php

namespace Fuel\Migrations;

class Add_government_code_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'government_code' => array('constraint' => 7,  'null' => true, 'type' => 'varchar'),
		));
		\DBUtil::create_index('divisions', 'government_code', 'idx_divisions_government_code');
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'government_code'
		));
	}
}
