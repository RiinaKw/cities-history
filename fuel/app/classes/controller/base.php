<?php

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
	protected $user = null;

	protected function user(): ?Model_User
	{
		return Helper_Session::user();
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
