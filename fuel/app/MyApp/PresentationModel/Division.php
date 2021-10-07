<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel;

use MyApp\Abstracts\PresentationModel;
use Model_Division;
use MyApp\Helper\Uri;

class Division extends PresentationModel
{
	protected $model = null;

	public function __construct(Model_Division $model)
	{
		$this->model = $model;
	}

	public function suffix_classification(): string
	{
		$arr = [
			'支庁'       => '支庁',
			'振興局'     => '支庁',
			'総合振興局' => '支庁',
			'都'         => true,
			'道'         => true,
			'府'         => true,
			'県'         => true,
			'市'         => true,
			'区'         => true,
			'郡'         => true,
		];

		$suffix = $this->model->suffix;
		if (! isset($arr[$suffix])) {
			return '町村';
		}
		$type = $arr[$suffix];
		return is_string($type) ? $type : $suffix;
	}

	public function source(): string
	{
		return \Helper_Html::wiki($this->model->source);
	}
	// function source()

	public function url(): string
	{
		return Uri::create(
			'division.detail',
			['path' => $this->model->path]
		);
	}

	public function htmlAnchor(string $label = ''): string
	{
		if (! $label) {
			$label = $this->model->fullname;
		}
		$attributes = [
			'class' => $this->model->is_unfinished ? 'unfinished' : '',
			'href' => $this->url(),
			'data-toggle' => 'tooltip',
			'title' => $this->model->path,
		];
		$attr_array = [];
		array_walk($attributes, function ($item, $key) use (&$attr_array) {
			$attr_array[] = "{$key}=\"{$item}\"";
		});
		$attr_html = implode(' ', $attr_array);
		return "<a {$attr_html}>{$label}</a>";
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
		$belongs = $this->model->belongs;
		if ($belongs) {
			return '<span class="belongs badge badge-semilight font-weight-light">'
				. $belongs->getter()->fullname
				. '</span>';
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
