<?php

/**
 * @package  App\Table
 */

namespace MyApp\Table;

/**
 * イベント詳細テーブルを管理するクラス
 */
class Event_detail extends \MyApp\Abstracts\Table
{
	public const TABLE_NAME = 'event_details';
	public const TABLE_PK = ['id'];
}
