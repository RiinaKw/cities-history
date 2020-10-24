<?php
/**
 * The Admin Controller.
 *
 * Admin controller for edit divisions.
 *
 * @package  app
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Divisions extends Controller_Admin_Base
{
	public function action_index()
	{
		$path = $this->param('path');
		$filter = Input::get('filter');
		/*
		if ($path)
		{
			$parent = Model_Division::get_by_path($path);
			$ids = Model_Division::get_by_parent_division_and_date($parent);
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
		}
		*/

		$parent = null;
		if ($path)
		{
			$parent = Model_Division::get_by_path($path);
		}
		$divisions = Model_Division::get_by_admin_filter($parent, $filter);

		foreach ($divisions as $division)
		{
			//$division = Model_Division::find_by_pk($id);
			$end_event = Model_Event::find_by_pk($division->end_event_id);

			$division->valid_kana = $division->name_kana && $division->suffix_kana;
			$division->valid_start_event = !! $division->start_event_id;
			$division->valid_end_event = !! $division->end_event_id;
			$division->valid_government_code =
				($division->suffix == '郡')
				||
				$division->government_code
				||
				$end_event && strtotime($end_event->date) < strtotime('1970-04-01');
			//$divisions[] = $division;
			$division->valid_source = !! strlen($division->source);
			$division->is_wikipedia = (stripos($division->source, 'wikipedia') !== false);
		}

		// create Presenter object
		$content = Presenter::forge(
			'admin/divisions/list',
			'view',
			null,
			'admin/admin_divisions.tpl'
		);
		$content->path = $path;
		$content->divisions = $divisions;

		return $content;
	} // function action_index()
} // class Controller_Admin_Divisions
