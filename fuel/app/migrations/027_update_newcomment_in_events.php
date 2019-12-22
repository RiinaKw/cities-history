<?php

namespace Fuel\Migrations;

class Update_newcomment_in_events
{
	public function up()
	{
		$events = \Model_Event::find_all();
		if ($events)
		{
			foreach ($events as $event)
			{
				$event->newcomment = $event->comment ?: null;
				$event->save();
			}
		}
	}

	public function down()
	{
		$events = \Model_Event::find_all();
		if ($events)
		{
			foreach ($events as $event)
			{
				$event->comment = $event->newcomment ?: null;
				$event->save();
			}
		}
	}
}
