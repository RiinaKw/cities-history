<?php

namespace Fuel\Migrations;

class Add_source_to_events
{
	public function up()
	{
		if (! \DBUtil::field_exists('events', ['source'])) {
			\DBUtil::add_fields('events', array(
				'source' => array('null' => true, 'type' => 'text'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('events', ['source'])) {
			\DBUtil::drop_fields('events', ['source']);
		}
	}
}
