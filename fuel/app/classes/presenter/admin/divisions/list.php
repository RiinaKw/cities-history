<?php

use MyApp\Helper\Uri;
use MyApp\Helper\Breadcrumb;

/**
 * @package  App\Presenter
 */
class Presenter_Admin_Divisions_List extends Presenter_Layout
{
	public function view()
	{
		foreach ($this->divisions as $division) {
			$division->url_belongto = Uri::create('admin.division.detail', ['path' => $division->path]);
		}

		$this->filters = [
			'' => 'フィルタなし',
			'empty_kana' => 'かな未入力',
			'empty_code' => '自治体コード未入力',
			'empty_source' => '出典未入力',
			'is_wikipedia' => '出典が Wikipedia',
		];
		$this->filter = Input::get('filter');

		$this->breadcrumbs = Breadcrumb::forge('自治体管理', Uri::create('admin.division.list'));
		if ($this->parent) {
			$this->breadcrumbs->division($this->parent);
		}

		$this->title = '自治体管理';
		$this->nav_item = 'admin-division';
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Admin_Divisions_List
