<?php

/**
 * @package  App\Helper
 */

namespace MyApp\PresentationModel;

class Division
{
	protected $model = null;

	public function __construct(\Model_Division $model)
	{
		$this->model = $model;
	}

	public function url()
	{
		return \Helper_Uri::create(
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
		if (\Input::get('debug') && $this->model->government_code) {
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

	public function validKana(): bool
	{
		return $this->model->name_kana && $this->model->suffix_kana;
	}

	public function validStart(): bool
	{
		return (bool)$this->model->start_event_id;
	}

	public function validEnd(): bool
	{
		return (bool)$this->model->end_event_id;
	}

	public function validCode(): bool
	{
		$end_event = \Model_Event::find_by_pk($this->model->end_event_id);
		return
			($this->model->suffix == '郡')
			||
			$this->model->government_code
			||
			$end_event && strtotime($end_event->date) < strtotime('1970-04-01');
	}

	public function validSource(): bool
	{
		return (bool)strlen($this->model->source);
	}

	public function isWikipedia(): bool
	{
		return stripos($this->model->source, 'wikipedia');
	}

	public function validAll(): bool
	{
		return
			$this->validKana()
			&& $this->validStart()
			&& $this->validEnd()
			&& $this->validCode()
			&& $this->validSource()
			&& ! $this->isWikipedia();
	}
}
