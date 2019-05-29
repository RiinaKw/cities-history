<?php

namespace Fuel\Migrations;

class Add_belongs_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('belongs_division_id')))
		{
			\DBUtil::add_fields('divisions', array(
				'belongs_division_id' => array('constraint' => 50,  'null' => true, 'type' => 'varchar'),
			));
		}
		\DBUtil::create_index('divisions', 'belongs_division_id', 'idx_divisions_belongs_division_id');
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('belongs_division_id')))
		{
			\DBUtil::drop_fields('divisions', array(
				'belongs_division_id',
			));
		}
	}
}
