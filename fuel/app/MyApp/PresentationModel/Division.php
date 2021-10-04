<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel;

use Model_Division;

class Division
{
	protected $model = null;

	public function __construct(Model_Division $model)
	{
		$this->model = $model;
	}

	public function suffix_classification(): string
	{
		switch ($this->model->suffix) {
			default:
				return '町村';

			case '都':
			case '道':
			case '府':
			case '県':
			case '市':
			case '区':
			case '郡':
				return $this->model->suffix;

			case '支庁':
			case '振興局':
			case '総合振興局':
				return '支庁';
		}
	}

	public function kana()
	{
		$ids = explode('/', substr($this->model->id_path, 0, -1));
		$kana = '';
		foreach ($ids as $id) {
			$parent = Model_Division::find($id);
			$kana .= ($kana ? '/' : '') . $parent->fullname_kana;
		}
		return $kana;
	}

	public function source(): string
	{
		return \Helper_Html::wiki($this->model->source);
	}
	// function source()

	public function url(): string
	{
		return \Helper_Uri::create(
			'division.detail',
			['path' => $this->model->path]
		);
	}

	public function htmlAnchor(string $label = ''): string
	{
		if (! $label) {
			$label = $this->model->fullname;
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
				$end_event = \Model_Event::find($d->end_event_id);
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
