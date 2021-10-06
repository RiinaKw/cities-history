<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

use MyApp\Helper\Session\Item as SessionItem;

abstract class Controller extends \Controller
{
	protected $session_user = null;

	protected function user(): ?\Model_User
	{
		return $this->session_user->get();
	}

	protected function requireUser(): void
	{
		$user = $this->user();
		if (! $user) {
			throw new \HttpNoAccessException('permission denied');
		}
		$this->user = $user;
	}

	protected function activity(string $target, int $id): void
	{
		\Model_Activity::insert_log([
			'user_id' => $this->user->id,
			'target' => $target,
			'target_id' => $id,
		]);
	}

	protected function redirect(string $config, array $params = [])
	{
		\Helper_Uri::redirect($config, $params);
	}

	public function before()
	{
		parent::before();

		\Config::load('uri', true);
		\Config::load('common', true);

		$this->session_user = new SessionItem('user');
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
