<?php
/**
 * The Admin Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Admin_Divisions extends Controller_Admin_Base
{
	public function action_index()
	{
		$path = $this->param('path');
		if ($path)
		{
			$parent = Model_Division::get_by_path($path);
			$ids = Model_Division::get_by_parent_division_id_and_date($parent->id);
			array_unshift($ids, $parent->id);
		}
		else
		{
			$ids = Model_Division::get_all_id();

			$top_arr = Model_Division::get_top_level();
			$ids = [];
			foreach ($top_arr as $d)
			{
				$ids[] = $d->id;
			}
			foreach ($top_arr as $d)
			{
				$parent = Model_Division::find_by_pk($d->id);
				$temp = Model_Division::get_by_parent_division_id_and_date($d->id);
				$ids = array_merge($ids, $temp);
			}
		}

		$divisions = [];
		foreach ($ids as $id)
		{
			$division = Model_Division::find_by_pk($id);
			$end_event = Model_Event::find_by_pk($division->end_event_id);

			$division->valid_kana = $division->name_kana && $division->postfix_kana;
			$division->valid_start_event = !! $division->start_event_id;
			$division->valid_end_event = !! $division->end_event_id;
			$division->valid_government_code =
				($division->postfix == '郡')
				||
				$division->government_code
				||
				$end_event && strtotime($end_event->date) < strtotime('1970-04-01');
			$divisions[] = $division;
		}

		// ビューを設定
		$content = View_Smarty::forge('admin/admin_divisions.tpl');
		$content = Presenter::forge(
			'admin/divisions/list',
			'view',
			null,
			'admin/admin_divisions.tpl'
		);
		$content->divisions = $divisions;

		return $content;
	} // function action_index()
} // class Controller_Admin
