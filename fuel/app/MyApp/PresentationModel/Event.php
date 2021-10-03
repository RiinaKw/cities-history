<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel;

use Model_Event;

class Event
{
	protected $model = null;

	public function __construct(Model_Event $model)
	{
		$this->model = $model;
	}

	public function source(): string
	{
		return \Helper_Html::wiki($this->model->source);
	}
	// function source()
}
