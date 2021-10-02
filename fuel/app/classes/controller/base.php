<?php

use MyApp\Helper\Session\Item as SessionItem;

/**
 * The Base Controller.
 *
 * Base class for controller.
 *
 * @package  App\Controller
 * @extends  Controller
 */
abstract class Controller_Base extends Controller
{
	protected $session_user = null;

	protected function user(): ?Model_User
	{
		$user_id = $this->session_user->get();
		return $user_id ? Model_User::find_by_pk($user_id) : null;
	}

	protected function requireUser(): void
	{
		$user = $this->user();
		if (! $user) {
			throw new HttpNoAccessException('permission denied');
		}
		$this->user = $user;
	}

	protected function activity(string $target, int $id): void
	{
		Model_Activity::insert_log([
			'user_id' => $this->user->id,
			'target' => $target,
			'target_id' => $id,
		]);
	}

	protected function redirect(string $config, array $params = [])
	{
		Helper_Uri::redirect($config, $params);
	}

	public function before()
	{
		parent::before();

		Config::load('uri', true);
		Config::load('common', true);

		$this->session_user = new SessionItem('user_id');
	}
	// function before()

	public function after($response)
	{
		$response = parent::after($response);
		return $response;
	}
	// function after()
}
// class Controller_Base
