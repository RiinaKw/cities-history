<?php

/**
 * @package  App\Presenter
 */
class Presenter_Division_Tree extends Presenter_Layout
{
	protected function _get_path($obj)
	{
		if (is_object($obj)) {
			$obj->path = $obj->get_path();
			$obj->url_detail = Helper_Uri::create(
				'division.detail',
				['path' => $obj->path]
			);
			$this->_get_path($obj->_children);
		} else {
			foreach ($obj as $item) {
				$this->_get_path($item);
			}
		}
	}
	// function _get_path()

	public function view()
	{
		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$cur_date) {
			$timestamp = strtotime($cur_date->date);
			$cur_date->year  = (int)date('Y', $timestamp);
			$cur_date->month = (int)date('m', $timestamp);
			$cur_date->day   = (int)date('d', $timestamp);
			$cur_date->url = Helper_Uri::create(
				'division.tree',
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
			'division.tree',
			['path' => $this->division->path]
		);

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$getter = $this->division->getter();

		$this->search_path = $getter->search_path;
		$this->search_path_kana = $getter->search_path_kana;

		$title = $this->division->path . 'の自治体一覧';
		$description = $this->division->path . 'の自治体一覧 ' . $this->search_path . ' ' . $this->search_path_kana;

		if ($this->date) {
			$description .= MyApp\Helper\Date::format(' Y(Jk)-m-d', $this->date);
		}

		$this->path_kana = $getter->path_kana;

		$this->title = $title;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = \MyApp\Helper\Breadcrumb::forge()->division($this->division);
		$this->show_share = true;

		$this->year_list = range(1878, date('Y'));
		$this->month_list = range(1, 12);
		$this->day_list = range(1, 31);
	}
	// function view()
}
// class Presenter_List_Detail
