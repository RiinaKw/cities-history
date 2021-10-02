<?php

use MyApp\Table\Division as DivisionTable;

/**
 * The Admin Controller.
 *
 * Admin controller for edit divisions.
 *
 * @package  App\Controller
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
			$parent = DivisionTable::get_by_path($path);
			$ids = DivisionTable::get_by_parent_division_and_date($parent);
			array_unshift($ids, $parent->id);
		}
		else
		{
			$ids = DivisionTable::get_all_id();

			$top_arr = DivisionTable::get_top_level();
			$ids = [];
			foreach ($top_arr as $d)
			{
				$ids[] = $d->id;
			}
		}
		*/

		$parent = null;
		if ($path) {
			$parent = DivisionTable::get_by_path($path);
		}
		$divisions = DivisionTable::get_by_admin_filter($parent, $filter);

		// create Presenter object
		$content = Presenter::forge(
			'admin/divisions/list',
			'view',
			null,
			'admin/admin_divisions.tpl'
		);
		$content->path = $path;
		$content->parent = $parent;
		$content->divisions = $divisions;

		return $content;
	}
	// function action_index()
}
// class Controller_Admin_Divisions
