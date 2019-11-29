<?php

class Presenter_Top extends Presenter_Layout
{
	public function view()
	{
		foreach ($this->divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$this->title = '';
		$this->description = '日本全国の市区町村合併をまとめたデータベースサイトです。';
		$this->og_type = 'website';

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_Top
