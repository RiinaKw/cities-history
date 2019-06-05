<?php

class Presenter_Division_Children extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		$this->url_detail = Helper_Uri::create('list.division', ['path' => $this->path]);
		$this->url_detail_timeline = Helper_Uri::create('division.detail', ['path' => $this->path]);
		$this->url_children_timeline = Helper_Division::get_children_url($this->path);;
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

		$breadcrumbs_arr = Helper_Breadcrumb::breadcrumb_and_kana($this->path);
		$breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$path_kana = $breadcrumbs_arr['path_kana'];
		$this->path_kana = $path_kana;

		if ($this->belongs_division)
		{
			$this->belongs_division->url_detail = Helper_Uri::create(
				'division.detail',
				['path' => $this->belongs_division->get_path(null, true)]
			);
		}

		// meta description
		$description = $this->path.'（'.$path_kana.') ';
		foreach ($this->events as $event)
		{
			$event_parent = Model_Event::find_by_pk($event->event_id);
			$date = Helper_Date::date('Y(Jk)-m-d', $event_parent->date);
			$description .= ' | '.$date.' '.$event_parent->type;
		}

		$layout->title = $this->path;
		$layout->description = $description;
		$layout->og_type = 'article';
		$layout->breadcrumbs = $breadcrumbs;

		$this->url_add = Helper_Uri::create('division.add');

		return $layout;
	}
}
