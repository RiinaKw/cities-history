<?php

namespace Fuel\Migrations;

class Rename_comment_in_events
{
	public function up()
	{
		\DBUtil::modify_fields('events', array(
			'newcomment' => array('null' => true, 'type' => 'text', 'name' => 'comment'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('events', array(
			'comment' => array('null' => true, 'type' => 'text', 'name' => 'newcomment'),
		));
	}
}
