<?php

/**
 * @package  App\Model
 */

class Model_Event_Detail extends \MyApp\Abstracts\ActiveRecord
{
	use MyApp\Traits\Model\Presentable;

	protected static $_table_name  = 'event_details';
	protected static $_primary_key = ['id'];
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	protected static $_belongs_to = [
		'event',
		'division',
	];

	protected static $pmodel_class = \MyApp\PresentationModel\Event\Detail::class;

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	public function validation()
	{
		$validation = Validation::forge(mt_rand());

		// 入力ルール
		$validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date', 'Y-m-d');
		$validation->add('result', 'イベント結果')
			->add_rule('required');

		return $validation;
	}
	// function validation()

	public function get_source()
	{
		$content = nl2br($this->source);

		$arrSource = [
			'[cite]',
			'[/cite]',
		];
		$arrDest = [
			'<cite>',
			'</cite>',
		];
		$content = str_replace($arrSource, $arrDest, $content);

		preg_match_all("/\[\[(?<expressoin>.*?)\]\]/", $content, $matches);
		if ($matches) {
			foreach ($matches[0] as $key => $base) {
				$expression = $matches['expressoin'][$key];
				$arr = explode('|', $expression);

				$url = array_shift($arr);
				$text = array_shift($arr);

				$attrs = [
					'href' => $url,
				];
				foreach ($arr as $item) {
					list($name, $value) = explode(':', $item, 2);
					$attrs[$name] = $value;
				}

				$html_attrs = [];
				foreach ($attrs as $name => $value) {
					$html_attrs[] = sprintf(
						'%s="%s"',
						$name,
						$value
					);
				}
				$html = '<a ' . trim(implode(' ', $html_attrs)) . '>' . trim($text) . '</a>';

				$content = str_replace($base, $html, $content);
			}
		}
		return $content;
	}

	public static function unify_geoshape($url)
	{
		return preg_replace('/^https?:\/\/geoshape\.ex\.nii\.ac\.jp\/city\/geojson\/(.+)$/', '$1', $url);
	}
}
// class Model_Event_Detail
