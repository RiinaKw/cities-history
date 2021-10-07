<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel\Event;

use MyApp\Abstracts\PresentationModel;
use Model_Event_Detail;

class Detail extends PresentationModel
{
	protected $model = null;

	public function __construct(Model_Event_Detail $model)
	{
		$this->model = $model;
	}

	public function geoshape(): ?string
	{
		if ($this->model->geoshape) {
			return \Helper_Uri::create(
				'geoshape',
				['path' => $this->model->geoshape]
			);
		}
		return null;
	}

	public function htmlClass(): string
	{
		switch ($this->model->result) {
			case '新設':
				return 'birth';
			case '編入':
				return 'transfer';
			case '廃止':
			case '分割廃止':
				return 'death';
		}
		return '';
	}

	public function isSplit(): bool
	{
		return $this->model->result === '分割廃止';
	}
}
