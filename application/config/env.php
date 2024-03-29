
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['connection'] = array(
	'default' => array(
		'driver'    => 'sqlsrv',

		// NOTE : LIVE DATABASE
		// 'host'      => '103.137.111.6',
		// 'port'		=> '14330',
		// 'database'  => 'gf_pos',
		// 'username'  => 'sa',
		// 'password'  => 'Mgb654321',

		// NOTE : LOCAL DATABASE
		'host'      => 'localhost',
		'database'  => 'gf_pos',
		'username'  => '',
		'password'  => '',

		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	),
);
