<?php

namespace Fuel\Migrations;

class Delete_comment_from_events
{
	public function up()
	{
		\DBUtil::drop_fields('events', array(
			'comment'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('events', array(
			'comment' => array('null' => true, 'type' => 'text'),
		));
	}
}
