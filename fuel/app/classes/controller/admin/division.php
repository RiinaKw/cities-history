<?php

use MyApp\Abstracts\AdminController;
use MyApp\Table\Division as DivisionTable;
use MyApp\Helper\Uri;

/**
 * The Admin Controller.
 *
 * Admin controller for edit divisions.
 *
 * @package  App\Controller
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Division extends AdminController
{
	public function action_index()
	{
		$path = $this->param('path');
		$filter = Input::get('filter');
		/*
		if ($path)
		{
			$parent = DivisionTable::findByPath($path);
			$ids = DivisionTable::get_by_parent_division_and_date($parent);
			array_unshift($ids, $parent->id);
		}
		else
		{
			$ids = DivisionTable::get_all_id();

			$top_arr = DivisionTable::get_top_level();
			$ids = [];
			foreach ($top_arr as $d)
			{
				$ids[] = $d->id;
			}
		}
		*/

		$parent = null;
		if ($path) {
			$parent = DivisionTable::findByPath($path);
		}
		$divisions = DivisionTable::get_by_admin_filter($parent, $filter);

		// create Presenter object
		$content = Presenter::forge(
			'admin/divisions/list',
			'view',
			null,
			'admin/admin_divisions.tpl'
		);
		$content->path = $path;
		$content->parent = $parent;
		$content->divisions = $divisions;

		return $content;
	}
	// function action_index()



	public function post_add()
	{
		$input = Input::post();
		$input['is_unfinished'] = isset($input['is_unfinished']) ? $input['is_unfinished'] : false;
		$input['fullname'] = $input['name'] . $input['suffix'];

		try {
			DB::start_transaction();

			$parent = DivisionTable::findByPath($input['parent']);
			$new = Model_Division::make($input)->makePath($parent);

			$this->activity('add division', $new->id);
			DB::commit_transaction();
		} catch (HttpBadRequestException $e) {
			// internal error
			DB::rollback_transaction();
			throw $e;
		} catch (Exception $e) {
			Debug::dump($e, $e->getTraceAsString());
			//exit;
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}

		Uri::redirect('division.detail', ['path' => $new->path]);
		return;
	}
	// function action_add()

	public function post_add_csv()
	{
		try {
			DB::start_transaction();

			$separator = Input::post('type') === 'tsv' ? "\t" : ',';
			$body = explode("\n", Input::post('body'));

			$heads = explode($separator, array_shift($body));
			foreach ($heads as &$item) {
				$item = trim($item);
				if ($item === 'code') {
					$item = 'government_code';
				}
			}

			foreach ($body as $line) {
				$line = trim($line);
				if (! $line) {
					continue;
				}
				$items = explode($separator, $line);
				$arr = [];
				$count = count($heads);
				for ($i = 0; $i < $count; ++$i) {
					$arr[ $heads[$i] ] = trim($items[$i]);
				}
				$arr['parent'] = dirname($arr['path']);

				$divisions = DivisionTable::set_path($arr['path']);
				$division = array_pop($divisions);
				$division->create($arr);
			}

			DB::commit_transaction();

			Uri::redirect('division.detail', ['path' => $division->get_path()]);
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			//Debug::dump($e);
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}

	public function action_edit()
	{
		$input = Input::post();
		$input['is_unfinished'] = isset($input['is_unfinished']) ? $input['is_unfinished'] : false;
		$input['fullname'] = $input['name'] . $input['suffix'];

		try {
			DB::start_transaction();

			$division = DivisionTable::findByPath($this->param('path'));

			$parent = DivisionTable::findByPath($input['parent']);
			$new = Model_Division::make($input, $division)->makePath($parent)->updateChild();

			$this->activity('edit division', $new->id);

			DB::commit_transaction();

			$path_new = $new->get_path();

			Uri::redirect('division.detail', ['path' => $path_new]);
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			Debug::dump($e);
			//throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}
	// function action_edit()

	public function action_delete()
	{
		$path = $this->param('path');
		$division = DivisionTable::findByPath($path);
		if (! $division) {
			throw new HttpNotFoundException('自治体が見つかりません。');
		}
		$old_id = $division->id;
		$parent = $division->parent();
		$division->delete();

		$this->activity('delete division', $old_id);

		if ($parent) {
			Uri::redirect('division.detail', ['path' => $parent->path]);
		} else {
			Uri::redirect('top');
		}
	}
	// function action_delete()
}
// class Controller_Admin_Divisions
