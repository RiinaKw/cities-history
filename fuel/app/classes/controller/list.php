<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;
use MyApp\Model\Division\Tree;
use MyApp\Helper\Uri;

/**
 * The List Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_List extends Controller
{
	public function action_index()
	{
		Uri::redirect('top');
	}
	// function action_index()

	public function action_detail()
	{
		$path = $this->param('path');

		$year = (int)Input::get('year');
		$month = (int)Input::get('month');
		$day = (int)Input::get('day');

		if ($year && $month && $day) {
			$date_str = $year . '-' . $month . '-' . $day;
			$timestamp = strtotime($date_str);
			$date = date('Y-m-d', $timestamp);
			$year = (int)date('Y', $timestamp);
			$month = (int)date('m', $timestamp);
			$day = (int)date('d', $timestamp);
		} else {
			$date = null;
			$year = 0;
			$month = 0;
			$day = 0;
		}

		$top_division = null;
		if ($path) {
			$top_division = DivisionTable::findByPath($path);
			if (! $top_division || $top_division->get_path() != $path) {
				throw new HttpNotFoundException('自治体が見つかりません。');
			}
		}

		$tree = Tree::create($top_division, $date);

		// create Presenter object
		$content = Presenter_Division_Tree::forge();
		$content->date = $date;
		$content->year = $year;
		$content->month = $month;
		$content->day = $day;
		$content->division = $top_division;
		$content->tree = $tree;
		return $content;
	}
	// function action_detail()
}
// class Controller_List
