<?php

use MyApp\Helper\Uri;
use MyApp\Helper\Breadcrumb;

/**
 * @package  Fuel\Presenter
 */
class Presenter_Division_Timeline extends Presenter_Layout
{
	public function view()
	{
		$getter = $this->division->getter();
		$path = $getter->path;

		$this->url_edit         = Uri::create('admin.division.edit', ['path' => $path]);
		$this->url_delete       = Uri::create('admin.division.delete', ['path' => $path]);

		$components = [
			'add_division'    => View_Smarty::forge('components/add_division.tpl'),
			'edit_division'   => View_Smarty::forge('components/edit_division.tpl'),
			'delete_division' => View_Smarty::forge('components/delete_division.tpl'),
			'change_event'    => View_Smarty::forge('components/change_event.tpl'),
		];
		$this->components = $components;

		// meta description
		$description = "{$path} ({$getter->path_kana}) {$getter->search_path} {$getter->search_path_kana}";

		foreach ($this->events as $event) {
			$date = MyApp\Helper\Date::format('Y(Jk)-m-d', $event->date);
			$description .= " | {$date} {$event->title}";

			$event->birth = false;
			$event->live = false;
			$event->death = false;
			if ($this->division->start_event_id === $event->id) {
				$event->birth = true;
			} elseif ($this->division->end_event_id === $event->id) {
				$event->death = true;
			}

			$result = Model_Event_Detail::query()
				->where('event_id', $event->id)
				->where('division_id', $this->division->id)
				->get();
			$detail = array_pop($result);

			if ($detail) {
				switch ($detail->result) {
					case '存続':
						$event->live = true;
						break;
					case '廃止':
					case '分割廃止':
						$detail->death = true;
						break;
				}
			}
		}
		// foreach ($events as $event)

		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = Breadcrumb::forge()->division($this->division);
		$this->show_share = true;
	}
	// function view()
}
// class Presenter_Division_Detail
