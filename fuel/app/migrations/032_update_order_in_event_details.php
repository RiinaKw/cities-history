<?php

namespace Fuel\Migrations;

class Update_order_in_event_details
{
	public function up()
	{
		$details = \Model_Event_Detail::find_all();
		if ($details)
		{
			foreach ($details as $detail)
			{
				$detail->order = $detail->no;
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
				$detail->no = $detail->order;
				$detail->save();
			}
		}
	}
}
