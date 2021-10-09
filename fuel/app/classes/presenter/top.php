<?php

/**
 * @package  Fuel\Presenter
 */
class Presenter_Top extends Presenter_Layout
{
	public function view()
	{
		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
			'add_divisions_csv' => View_Smarty::forge('components/add_divisions_csv.tpl'),
		];
		$this->components = $components;

		$this->title = '';
		$this->description = '日本全国の市区町村合併をまとめたデータベースサイトです。';
		$this->og_type = 'website';
		$this->show_share = true;
	}
	// function view()
}
// class Presenter_Top
