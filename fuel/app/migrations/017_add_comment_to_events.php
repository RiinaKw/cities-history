<?php

namespace Fuel\Migrations;

class Add_comment_to_events
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('events', array('comment')))
		{
			\DBUtil::add_fields('events', array(
				'comment' => array('null' => true, 'type' => 'text'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('events', array('comment')))
		{
			\DBUtil::drop_fields('events', array(
				'comment',
			));
		}
	}
}
