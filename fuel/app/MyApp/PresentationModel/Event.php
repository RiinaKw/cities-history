<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel;

use MyApp\Abstracts\PresentationModel;
use Model_Event;

class Event extends PresentationModel
{
	protected $model = null;

	public function __construct(Model_Event $model)
	{
		$this->model = $model;
	}

	public function source(): string
	{
		return \MyApp\Helper\Str::wiki($this->model->source);
	}
	// function source()
}
