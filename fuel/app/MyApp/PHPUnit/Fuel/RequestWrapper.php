<?php

namespace MyApp\PHPUnit\Fuel;

use PHPUnit\Framework\TestCase;
use Request;
use Fuel\Core\HttpException;

abstract class RequestWrapper extends TestCase
{
	private $request = null;
	private $response = null;

	/**
	 * リクエストを実行していなければ実行し、結果を保存
	 * HTTP エラーが発生した場合は、アプリケーションで指定されたエラーページを取得
	 */
	private function execute(): void
	{
		if (! $this->response) {
			try {
				$this->response = $this->request->execute()->response();
			} catch (HttpException $e) {
				// HTTP 関連の例外をルーティング
				$exceptions = [
					\Fuel\Core\HttpBadRequestException::class => '_400_',
					\Fuel\Core\HttpNoAccessException::class => '_403_',
					\Fuel\Core\HttpNotFoundException::class => '_404_',
					\Fuel\Core\HttpServerErrorException::class => '_500_',
				];

				// 例外のクラス名からルーティングを取得
				$class = get_class($e);
				if (! isset($exceptions[$class])) {
					throw new \Exception("Unknown exception : '{$class}'");
				}
				$name = $exceptions[$class];
				$route = \Router::$routes[$name]->translation;

				// _404_ などの特殊ルーティングを適用
				$this->response = Request::forge($route, false)->execute(array($e))->response();
			}
		}
	}

	/**
	 * 実行するリクエストを設定
	 * @param string $path  ルーティングパス
	 */
	public function request(string $path): void
	{
		$this->request = Request::forge($path);
		$_SERVER['PATH_INFO'] = $path;
	}

	/**
	 * 実行結果にテキストが含まれるかどうか
	 * @param string $pattern  検索する正規表現
	 */
	public function assertContainRe(string $pattern): void
	{
		$this->execute();
		$this->assertTrue(
			(bool)preg_match(
				$pattern,
				$this->response->body()->render()
			)
		);
	}

	/**
	 * 実行結果のステータスコード
	 * @param int $expected  想定されるステータスコード
	 */
	public function assertStatus(int $expected): void
	{
		$this->execute();
		$this->assertSame($expected, $this->response->status);
	}

	public function assertException(string $classname): void
	{
		$this->expectException($classname);
		$this->execute();
	}
}
