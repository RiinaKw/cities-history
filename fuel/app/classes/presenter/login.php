<?php

class Presenter_Login extends Presenter
{
	public function view()
	{
		$this->url_login = Helper_Uri::create('login', [], ['url' => Input::get('url')]);
	}
}
