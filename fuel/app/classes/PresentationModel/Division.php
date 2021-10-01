<?php

class PresentationModel_Division
{
	protected $model = null;

	public function __construct(Model_Division $model)
	{
		$this->model = $model;
	}

	public function url()
	{
		return Helper_Uri::create(
			'division.detail',
			['path' => $this->model->path]
		);
	}
}
