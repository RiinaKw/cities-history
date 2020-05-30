<?php

namespace Fuel\Migrations;

class Update_source_in_events
{
	public function up()
	{
		$events = \Model_Event::find_all();
		if ($events)
		{
			foreach ($events as $event)
			{
				$event->source = 'Wikipedia';
				$event->save();
			}
		}
	}

	public function down()
	{
	}
}
