<?php

namespace Fuel\Migrations;

class Update_new_columns_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
			if ( isset($division->new_government_code) && isset($division->government_code) )
				{
					$division->new_government_code = $division->government_code;
				}
				if ( isset($division->new_display_order) && isset($division->display_order) )
				{
					$division->new_display_order = $division->display_order;
				}
				if ( isset($division->new_end_date) && isset($division->end_date) )
				{
					$division->new_end_date = $division->end_date;
				}
				if ( isset($division->new_is_unfinished) && isset($division->is_unfinished) )
				{
					$division->new_is_unfinished = ($division->new_end_date == '9999-12-31');
				}
				if ( isset($division->new_is_empty_government_code) && isset($division->is_empty_government_code) )
				{
					$division->new_is_empty_government_code = empty($division->new_government_code);
				}
				if ( isset($division->new_is_empty_kana) && isset($division->is_empty_kana) )
				{
					$division->new_is_empty_kana = empty($division->name_kana);
				}
				$division->save();
			}
		}
	}

	public function down()
	{
		$divisions = \Model_Division::find_all();
		if ($divisions)
		{
			foreach ($divisions as $division)
			{
			if ( isset($division->new_government_code) && isset($division->government_code) )
				{
					$division->new_government_code = $division->government_code;
				}
				if ( isset($division->new_display_order) && isset($division->display_order) )
				{
					$division->new_display_order = $division->display_order;
				}
				if ( isset($division->new_end_date) && isset($division->end_date) )
				{
					$division->new_end_date = $division->end_date;
				}
				if ( isset($division->new_is_unfinished) && isset($division->is_unfinished) )
				{
					$division->new_is_unfinished = $division->is_unfinished;
				}
				if ( isset($division->new_is_empty_government_code) && isset($division->is_empty_government_code) )
				{
					$division->new_is_empty_government_code = $division->is_empty_government_code;
				}
				if ( isset($division->new_is_empty_kana) && isset($division->is_empty_kana) )
				{
					$division->new_is_empty_kana = $division->is_empty_kana;
				}
				$division->save();
			}
		}
	}
}
