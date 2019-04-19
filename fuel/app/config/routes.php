<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	'add(/:path)?'  => 'add/index',

	'view/list' => 'view/list',
	'view(/:path)?' => 'view/index',

	'event/(.+?)' => 'rest/event/detail/$1',
);
