<?php

class Presenter_List_Search extends Presenter_Layout
{
	public function view()
	{
		$this->title = '自治体検索';
		$this->description = '自治体検索検索結果 : '.$this->q;
		$this->robots = 'noindex,nofollow';
		$this->og_type = 'article';
		$this->breadcrumbs = ['検索' => ''];
	} // function view()
} // class Presenter_List_Search
