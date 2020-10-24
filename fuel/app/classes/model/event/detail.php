<?php

class Model_Event_Detail extends Model_Base
{
	protected static $_table_name  = 'event_details';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation($is_new = false, $factory = null)	// 引数は単なる識別子、何でもいい
	{
		$validation = Validation::forge($factory);

		// 入力ルール
		$field = $validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date', 'Y-m-d');
		$field = $validation->add('result', 'イベント結果')
			->add_rule('required');

		return $validation;
	} // function validation()

	public static function get_by_division_id($division_id, $start_date = null, $end_date = null)
	{
		$query = DB::select('d.*', 'e.title', 'e.date', 'e.comment', 'e.source')
			->from([self::$_table_name, 'd'])
			->join(['events', 'e'])
			->on('e.id', '=', 'd.event_id')
			->where('d.is_refer', '=', false)
			->where('e.deleted_at', '=', null)
			->where('d.deleted_at', '=', null);
		if (is_array($division_id))
		{
			$query->where('d.division_id', 'in', $division_id);
		}
		else
		{
			$query->where('d.division_id', '=', $division_id);
		}
		if ($start_date)
		{
			$query->where('e.date', '>=', $start_date);
		}
		if ($end_date)
		{
			$query->where('e.date', '<=', $end_date);
		}
		$query->order_by('e.date', 'desc');

		return $query->as_object('Model_Event_Detail')->execute()->as_array();
	} // function get_by_division_id()

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
} // class Model_Event_Detail
