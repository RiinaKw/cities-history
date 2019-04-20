<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	'add(/:path)?'  => 'add/index',

	'view' => 'view/list',
	'view(/:path)?' => 'view/index',
	'belongto(/:path)?' => 'view/belongto',

	'event/(.+?)' => 'rest/event/detail/$1',
);
