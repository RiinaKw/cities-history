<?php

use MyApp\Helper\Uri;
use MyApp\Helper\Breadcrumb;

/**
 * @package  Fuel\Presenter
 */
class Presenter_Division_Tree extends Presenter_Layout
{
	public function view()
	{
		$this->parent = $this->tree->self();
		$getter = $this->parent->getter();

		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$cur_date) {
			$timestamp = strtotime($cur_date->date);
			$cur_date->year  = (int)date('Y', $timestamp);
			$cur_date->month = (int)date('m', $timestamp);
			$cur_date->day   = (int)date('d', $timestamp);
			$cur_date->url = Uri::create(
				'division.tree',
				['path' => $this->parent->path],
				[
					'year'  => $cur_date->year,
					'month' => $cur_date->month,
					'day'   => $cur_date->day,
				]
			);
		}
		$this->reference_dates = $dates;
		$this->url_all = Uri::create(
			'division.tree',
			['path' => $this->parent->path]
		);

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$title = $getter->path . 'の自治体一覧';
		$description = "{$title} {$getter->search_path} {$getter->search_path_kana}";

		if ($this->date) {
			$description .= MyApp\Helper\Date::format(' Y(Jk)-m-d', $this->date);
		}

		$this->title = $title;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = Breadcrumb::forge()->division($this->parent);
		$this->show_share = true;

		$this->year_list = range(1878, date('Y'));
		$this->month_list = range(1, 12);
		$this->day_list = range(1, 31);
	}
	// function view()
}
// class Presenter_List_Detail
