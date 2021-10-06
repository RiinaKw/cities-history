<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel;

use MyApp\Abstracts\PresentationModel;
use MyApp\Model\File as Model;
use MyApp\Helper\Number;

class File extends PresentationModel
{
	protected $model = null;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function bytes_formatted(): string
	{
		return Number::format($this->model->bytes, 1024, ['B', 'KiB', 'MiB', 'GiB', 'TiB']);
	}
}
