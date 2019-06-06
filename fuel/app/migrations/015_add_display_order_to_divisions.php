<?php

namespace Fuel\Migrations;

class Add_display_order_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('display_order')))
		{
			\DBUtil::add_fields('divisions', array(
				'display_order' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('display_order')))
		{
			\DBUtil::drop_fields('divisions', array(
				'display_order',
			));
		}
	}
}
