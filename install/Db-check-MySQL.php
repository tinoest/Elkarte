<?php

$databases['mysql'] = array(
	'name' => 'MySQL',
	'extension' => 'MySQL Improved (MySQLi)',
	'version' => '5.0.19',
	'version_check' => 'return min(mysqli_get_server_info($db_connection), mysqli_get_client_info($db_connection));',
	'supported' => function_exists('mysqli_connect'),
	'additional_file' => '',
	'default_user' => 'mysqli.default_user',
	'default_password' => 'mysqli.default_password',
	'default_host' => 'mysqli.default_host',
	'default_port' => 'mysqli.default_port',
	'utf8_support' => true,
	'utf8_version' => '4.1.0',
	'utf8_version_check' => 'return mysqli_get_server_info($db_connection);',
	'alter_support' => true,
	'validate_prefix' => function (&$value) {
		$value = preg_replace('~[^A-Za-z0-9_\$]~', '', $value);
		return true;
	},
	'require_db_confirm' => true,
);