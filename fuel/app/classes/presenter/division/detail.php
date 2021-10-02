<?php

/**
 * @package  App\Presenter
 */
class Presenter_Division_Detail extends Presenter_Layout
{
	public function view()
	{
		$this->url_detail = Helper_Uri::create('list.division', ['path' => $this->path]);
		$this->url_detail_timeline = Helper_Uri::create('division.detail', ['path' => $this->path]);
		$this->url_children_timeline = Helper_Division::get_children_url($this->path);
		$this->url_add = Helper_Uri::create('division.add');
		$this->url_edit = Helper_Uri::create('division.edit', ['path' => $this->path]);
		$this->url_delete = Helper_Uri::create('division.delete', ['path' => $this->path]);
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

		$breadcrumbs_arr = Helper_Breadcrumb::breadcrumb_and_kana($this->division);
		$breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$path_kana = $breadcrumbs_arr['path_kana'];
		$this->path_kana = $path_kana;

		$this->search_path = $this->division->make_search_path();
		$this->search_path_kana = $this->division->make_search_path_kana();

		$this->belongs_division = Model_Division::find_by_pk($this->division->belongs_division_id);

		// meta description
		$description = "{$this->path} ({$path_kana})) {$this->search_path} {$this->search_path_kana}";

		foreach ($this->events as $event) {
			$event_parent = Model_Event::find_by_pk($event->event_id);
			$date = MyApp\Helper\Date::format('Y(Jk)-m-d', $event_parent->date);
			$description .= " | {$date} {$event_parent->title}";
		}

		$this->title = $this->path;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = $breadcrumbs;
		$this->show_share = true;

		$this->url_add = Helper_Uri::create('division.add');
	}
	// function view()
}
// class Presenter_Division_Detail
