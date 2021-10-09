<?php

namespace Fuel\Migrations;

class Update_path_and_path_kana_in_divisions
{
	public function up()
	{
		$divisions = \Model_Division::query();
		if ($divisions) {
			foreach ($divisions as $division) {
				$cur_division = $division;
				$kana = '';
				while ($cur_division) {
					$cur_kana = $cur_division->name_kana;
					if ($cur_division->show_suffix) {
						$cur_kana .= $cur_division->suffix_kana;
					}
					$kana = $cur_kana . '/' . $kana;
					$cur_division = \Model_Division::find($cur_division->parent_division_id);
				}
				$kana = mb_substr($kana, 0, mb_strlen($kana) - 1);
				$division->path_kana = $kana;

				$division->save();
			}
		}
	}

	public function down()
	{
	}
}
