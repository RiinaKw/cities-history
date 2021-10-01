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

	public function htmlAnchor(string $label = ''): string
	{
		if (! $label) {
			$label = $this->model->get_fullname();
		}
		$url = $this->url();
		$class = $this->model->is_unfinished ? 'unfinished' : '';
		return "<a class=\"{$class}\" href=\"{$url}\">{$label}</a>";
	}

	public function htmlDebugCode(): string
	{
		if (Input::get('debug') && $this->model->government_code) {
			return '<span class="government_code">' . $this->model->government_code . '</span>';
		} else {
			return '';
		}
	}

	public function htmlBelongs(): string
	{
		$belongs = $this->model->get_belongs_name();
		if ($belongs) {
			return "<span class=\"belongs badge badge-semilight font-weight-light\">{$belongs}</span>";
		} else {
			return '';
		}
	}
}
