<?php

namespace Fuel\Migrations;

class Update_new_geoshape_and_new_is_refer_in_event_details
{
	public function up()
	{
		$details = \Model_Event_Detail::find_all();
		if ($details)
		{
			foreach ($details as $detail)
			{
				$detail->new_is_refer = $detail->is_refer;
				$detail->new_geoshape = $detail->geoshape ?: null;
				$detail->save();
			}
		}
	}

	public function down()
	{
		$details = \Model_Event_Detail::find_all();
		if ($details)
		{
			foreach ($details as $detail)
			{
				$detail->is_refer = $detail->new_is_refer;
				$detail->geoshape = $detail->new_geoshape;
				$detail->save();
			}
		}
	}
}
