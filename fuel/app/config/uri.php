<?php

return array(
	'top'             => '',
	'login'           => 'login',
	'logout'          => 'logout',
	'reissue'         => 'reissue',
	'withdraw'        => 'withdraw',
	'impression'      => 'impression',
	'switch_login'    => 'switch',

	'event' => array(
		'add'      => 'event/add',
		'detail'   => 'event/:id.json',
		'edit'     => 'event/:id/edit',
		'delete'   => 'event/:id/delete',
	),
	'division' => array(
		'detail'   => 'view/:path',
		'belongto' => 'belongto/:path',
		'edit'     => 'edit/:path',
	),
);
