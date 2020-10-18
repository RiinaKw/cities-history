<?php

class Presenter_List_Detail extends Presenter_Layout
{
	protected function _get_path($obj)
	{
		if (is_object($obj))
		{
			$obj->path = $obj->get_path(null, true);
			$obj->url_detail = Helper_Uri::create(
				'division.detail',
				['path' => $obj->path]
			);
			$this->_get_path($obj->_children);
		}
		else
		{
			foreach ($obj as $item)
			{
				$this->_get_path($item);
			}
		}
	} // function _get_path()

	public function view()
	{
		$this->_get_path($this->division);
		foreach ($this->tree as $child)
		{
			$this->_get_path($child);
		}

		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$cur_date)
		{
			$timestamp = strtotime($cur_date->date);
			$cur_date->year  = (int)date('Y', $timestamp);
			$cur_date->month = (int)date('m', $timestamp);
			$cur_date->day   = (int)date('d', $timestamp);
			$cur_date->url = Helper_Uri::create(
				'list.division',
				['path' => $this->division->path],
				[
					'year'  => $cur_date->year,
					'month' => $cur_date->month,
					'day'   => $cur_date->day,
				]
			);
		}
		$this->reference_dates = $dates;
		$this->url_all = Helper_Uri::create(
			'list.division',
			['path' => $this->division->path]
		);
		$this->url_add = Helper_Uri::create('division.add');

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$title = $this->division->path.'の自治体一覧';
		$description = $this->division->path.'の自治体一覧';

		if ($this->date)
		{
			$description .= Helper_Date::format(' Y(Jk)-m-d', $this->date);
		}

		$breadcrumbs_arr = Helper_Breadcrumb::breadcrumb_and_kana($this->division->path);
		$this->path_kana = $breadcrumbs_arr['path_kana'];

		$this->title = $title;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$this->show_share = true;

		$this->year_list = range(1878, date('Y'));
		$this->month_list = range(1, 12);
		$this->day_list = range(1, 31);

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_List_Index
