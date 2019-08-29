<?php

class Presenter_List2_Index extends Presenter_Layout
{
	protected function _get_path($arr)
	{
		foreach ($arr as $item)
		{
			$item->path = $item->get_path(null, true);
			$item->url_detail = Helper_Uri::create(
				'division.detail',
				['path' => $item->path]
			);
		}
	} // function _get_path()

	public function view()
	{
		$this->_get_path($this->divisions);

		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$cur_date)
		{
			$cur_date->url = Helper_Uri::create(
				'list.division',
				['path' => ''],
				['date' => $cur_date->date]
			);
		}
		$this->reference_dates = $dates;
		$this->url_all = Helper_Uri::create(
			'list.division',
			['path' => '']
		);
		$this->url_add = Helper_Uri::create('division.add');

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$title = '自治体一覧';
		$description = '全国の自治体一覧';

		$this->title = $title;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = [];

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_List_Index
