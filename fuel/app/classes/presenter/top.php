<?php

class Presenter_Top extends Presenter_Layout
{
	public function view()
	{
		foreach ($this->divisions as &$division)
		{
			$division->path = $division->get_path();
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
			'add_divisions_csv' => View_Smarty::forge('components/add_divisions_csv.tpl'),
		];
		$this->components = $components;

		$this->title = '';
		$this->description = '日本全国の市区町村合併をまとめたデータベースサイトです。';
		$this->og_type = 'website';
		$this->show_share = true;

		$this->url_add = Helper_Uri::create('division.add');
		$this->url_add_csv = Helper_Uri::create('division.add_csv');
	} // function view()
} // class Presenter_Top
