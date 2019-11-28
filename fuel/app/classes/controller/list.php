<?php
/**
 * The List Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_List extends Controller_Base
{
	public function action_index()
	{
		Helper_Uri::redirect('top');
	} // function action_index()

	public function action_detail()
	{
		$path = $this->param('path');

		$year = (int)Input::get('year');
		$month = (int)Input::get('month');
		$day = (int)Input::get('day');

		if ($year && $month && $day)
		{
			$date_str = $year . '-' . $month . '-' . $day;
			$timestamp = strtotime($date_str);
			$date = date('Y-m-d', $timestamp);
			$year = (int)date('Y', $timestamp);
			$month = (int)date('m', $timestamp);
			$day = (int)date('d', $timestamp);
		}
		else
		{
			$date = null;
			$year = 0;
			$month = 0;
			$day = 0;
		}

		$top_division = null;
		if ($path)
		{
			$top_division = Model_Division::get_by_path($path);
			if ( ! $top_division || $top_division->get_path(null, true) != $path)
			{
				throw new HttpNotFoundException('自治体が見つかりません。');
			}
		}

		// create tree
		$result = $top_division->get_tree($date);

		// create Presenter object
		$content = Presenter::forge('list/detail', 'view', null, 'list.tpl');
		$content->date = $date;
		$content->year = $year;
		$content->month = $month;
		$content->day = $day;
		$content->division = $top_division;
		$content->tree = $result['tree'];
		$content->count = $result['count'];

		return $content;
	} // function action_detail()

	public function action_search()
	{
		$q = Input::get('q');
		$result = Model_Division::search($q);

		foreach ($result as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		// create Presenter object
		$content = Presenter::forge('list/search', 'view', null, 'search.tpl');
		$content->divisions = $result;
		$content->q = $q;

		return $content;
	} // function action_search()
} // class Controller_List
