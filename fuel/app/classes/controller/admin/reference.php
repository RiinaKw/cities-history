<?php

use MyApp\Abstracts\AdminController;
use MyApp\Traits\Controller\ModelRelated;
use MyApp\Helper\Session\Flash as FlashSession;

/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  App\Controller
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Reference extends AdminController
{
	use ModelRelated;

	protected $session_flash = null;

	protected static function getModelClass(): string
	{
		return Model_Referencedate::class;
	}

	public function before()
	{
		parent::before();

		$this->session_flash = new FlashSession('admin_data.reference');
	}

	public function action_list()
	{
		// create Presenter object
		$content = Presenter::forge(
			'admin/reference/list',
			'view',
			null,
			'admin/admin_reference.tpl'
		);
		$content->flash = $this->session_flash->get();

		return $content;
	}
	// function action_list()

	public function action_add()
	{
		Debug::dump(Input::post());
		try {
			DB::start_transaction();

			$reference = Model_Referencedate::forge([
				'date' => Input::post('date'),
				'description' => Input::post('description'),
			]);
			$reference->save();

			DB::commit_transaction();

			$this->activity('add reference date', $reference->id);
			$this->session_flash->set([
				'status'  => 'success',
				'message' => '追加に成功しました。',
			]);
			Helper_Uri::redirect('admin.reference.list');
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}
	// function action_add()

	public function action_edit($id)
	{
		$reference = static::getModel($id);
		try {
			DB::start_transaction();

			$reference->date = Input::post('date');
			$reference->description = Input::post('description');
			$reference->save();

			$this->activity('edit reference date', $reference->id);
			$this->session_flash->set([
				'status'  => 'success',
				'message' => '更新に成功しました。',
			]);
			DB::commit_transaction();

			Helper_Uri::redirect('admin.reference.list');
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}
	// function action_edit()

	public function action_delete($id)
	{
		$reference = static::getModel($id);
		try {
			DB::start_transaction();

			$id = $reference->id;
			$reference->delete();

			DB::commit_transaction();

			$this->activity('delete reference date', $id);
			$this->session_flash->set([
				'status'  => 'success',
				'message' => '削除に成功しました。',
			]);
			Helper_Uri::redirect('admin.reference.list');
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}
	// function action_delete()
}
// class Controller_Admin
