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

	public function isValid(string $type): bool
	{
		$def = [
			'kana' => function ($d) {
				return $d->name_kana && $d->suffix_kana;
			},
			'start' => function ($d) {
				return (bool)$d->start_event_id;
			},
			'end' => function ($d) {
				return (bool)$d->end_event_id;
			},
			'source' => function ($d) {
				return (bool)strlen($this->model->source);
			},
			'code' => function ($d) {
				$end_event = \Model_Event::find_by_pk($d->end_event_id);
				return
					($d->suffix == '郡')
					||
					$d->government_code
					||
					$end_event && strtotime($end_event->date) < strtotime('1970-04-01');
			},
		];
		return $def[$type] ? $def[$type]($this->model) : false;
	}

	public function isWikipedia(): bool
	{
		return stripos($this->model->source, 'wikipedia');
	}

	public function isValidAll(): bool
	{
		return
			$this->isValid('kana')
			&& $this->isValid('start')
			&& $this->isValid('end')
			&& $this->isValid('code')
			&& $this->isValid('source')
			&& ! $this->isWikipedia();
	}
}
