<?php

namespace Fuel\Migrations;

class Add_source_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('source')))
		{
			\DBUtil::add_fields('divisions', array(
				'source' => array('null' => true, 'type' => 'text'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('source')))
		{
			\DBUtil::drop_fields('divisions', array(
				'source',
			));
		}
	}
}
