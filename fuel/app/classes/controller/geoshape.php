<?php

/**
 * The Geoshape Controller.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Geoshape extends Controller
{
	public function post_index()
	{
		$path = Input::get('path');
		$url = 'https://geoshape.ex.nii.ac.jp/city/geojson/' . $path;

		$option = [
			CURLOPT_RETURNTRANSFER => true, //文字列として返す
			CURLOPT_TIMEOUT        => 3, // タイムアウト時間
		];

		$ch = curl_init($url);
		curl_setopt_array($ch, $option);

		$json    = curl_exec($ch);
		$info    = curl_getinfo($ch);
		$errorNo = curl_errno($ch);

		// OK以外はエラーなので空白配列を返す
		if ($errorNo !== CURLE_OK) {
			// 詳しくエラーハンドリングしたい場合はerrorNoで確認
			// タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
			return [];
		}

		// 200以外のステータスコードは失敗とみなし空配列を返す
		if ($info['http_code'] !== 200) {
			return [];
		}

		// 文字列から変換
		//$jsonArray = json_decode($json, true);

		echo $json;
		return;
	}
	// function action_index()
}
// class Controller_Geoshape
