<?php

namespace Fuel\Migrations;

class Update_sort_columns_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions) {
			foreach ($divisions as $division) {
				$division->is_empty_government_code = empty($division->government_code);
				$division->is_empty_kana = empty($division->name_kana);
				$end_event = \Model_Event::find_by_pk($division->end_event_id);
				if ($end_event) {
					$division->end_date = $end_event->date;
				}
				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
