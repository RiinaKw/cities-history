<?php

/**
 * @package  App\Presenter
 */
class Presenter_Division_Children extends Presenter_Layout
{
	public function view()
	{
		$getter = $this->division->getter();
		$path = $getter->path;

		$this->url_detail = Helper_Uri::create('list.division', ['path' => $path]);
		$this->url_detail_timeline = Helper_Uri::create('division.detail', ['path' => $path]);
		$this->url_children_timeline = Helper_Division::get_children_url($path);
		$this->url_add = Helper_Uri::create('division.add');
		$this->url_edit = Helper_Uri::create('division.edit', ['path' => $path]);
		$this->url_delete = Helper_Uri::create('division.delete', ['path' => $path]);
		$this->url_event_detail = Helper_Uri::create('event.detail');
		$this->url_event_add = Helper_Uri::create('event.add');
		$this->url_event_edit = Helper_Uri::create('event.edit');
		$this->url_event_delete = Helper_Uri::create('event.delete');

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
			'edit_division' => View_Smarty::forge('components/edit_division.tpl'),
			'delete_division' => View_Smarty::forge('components/delete_division.tpl'),
			'change_event' => View_Smarty::forge('components/change_event.tpl'),
		];
		$this->components = $components;

		// meta description
		//$description = "{$path} ({$path_kana})";
		/*
		foreach ($this->events as $event) {
			$event_parent = Model_Event::find_by_pk($event->event_id);
			$date = MyApp\Helper\Date::format('Y(Jk)-m-d', $event_parent->date);
			$description .= " | {$date} {$event_parent->title}";
		}
		*/

		$title = $path . 'の所属自治体タイムライン';

		$this->title = $title;
		$this->description = "{$title} {$getter->search_path} {$getter->search_path_kana}";
		$this->og_type = 'article';
		$this->breadcrumbs = \MyApp\Helper\Breadcrumb::division($this->division);
		$this->show_share = true;

		$this->url_add = Helper_Uri::create('division.add');
	}
	// function view()
}
// class Presenter_Division_Children
