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

	protected static $suffixes = [
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

	protected static $periods = [
		'平成～令和' => [
			'label' => '平成～令和',
			'start' => '1989-01-01',
			'end' => '2019-04-01',
		],
		'昭和後期' => [
			'label' => '昭和後期',
			'start' => '1950-01-01',
			'end' => '1988-12-31',
		],
		'大正～昭和前期' => [
			'label' => '大正～昭和前期',
			'start' => '1912-01-01',
			'end' => '1949-12-31',
		],
		'明治' => [
			'label' => '明治',
			'start' => '1878-01-01',
			'end' => '1911-12-31',
		],
	];

	public function __construct(Model_Division $model)
	{
		$this->model = $model;
	}

	public function suffix_classification(): string
	{
		$suffix = $this->model->suffix;
		if (! isset(static::$suffixes[$suffix])) {
			return '町村';
		}
		$type = static::$suffixes[$suffix];
		return is_string($type) ? $type : $suffix;
	}

	public function source(): string
	{
		return \MyApp\Helper\Str::wiki($this->model->source);
	}
	// function source()

	/**
	 * 自治体に関する各種 URI を生成
	 * @param  string $type  取得する URI の種別
	 * @return string|null   生成された URL
	 */
	public function uri(string $type): ?string
	{
		switch ($type) {
			case 'timeline':
				// 自治体タイムラインの URI
				return Uri::division($this->model);

			case 'tree':
				// 自治体ツリーの URI
				return Uri::create(
					'division.tree',
					['path' => $this->model->path]
				);

			case 'edit':
				// 自治体変更 Ajax の URL
				return Uri::create(
					'admin.division.edit',
					['path' => $this->model->path]
				);

			case 'delete':
				// 自治体削除 Ajax の URL
				return Uri::create(
					'admin.division.delete',
					['path' => $this->model->path]
				);
		}

		return null;
	}

	/**
	 * 子孫自治体タイムラインの URI の配列
	 * @return array
	 */
	public function urlListChildren(): array
	{
		$arr = [];
		foreach (static::$periods as $params) {
			$params['path'] = $this->model->path;
			$params['label'] = $params['label'];
			$arr[$params['label']] = Uri::create('division.children', $params);
		}
		return $arr;
	}
	// function urlListChildren()

	public function htmlAnchor(string $label = ''): string
	{
		if (! $label) {
			$label = $this->model->fullname;
		}
		$attributes = [
			'class' => $this->model->is_unfinished ? 'unfinished' : '',
			'href' => $this->uri('timeline'),
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

	public function htmlAnchorPath(): string
	{
		return $this->htmlAnchor($this->model->path);
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
