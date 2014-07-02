<?php

$databases['postgresql'] = array(
	'name' => 'PostgreSQL',
	'extension' => 'PostgreSQL (PgSQL)',
	'version' => '8.3',
	'function_check' => 'pg_connect',
	'version_check' => '$request = pg_query(\'SELECT version()\'); list ($version) = pg_fetch_row($request); list ($pgl, $version) = explode(" ", $version); return $version;',
	'supported' => function_exists('pg_connect'),
	'additional_file' => 'install_' . DB_SCRIPT_VERSION . '_postgresql.sql',
	'utf8_support' => true,
	'utf8_version' => '8.0',
	'utf8_version_check' => '$request = pg_query(\'SELECT version()\'); list ($version) = pg_fetch_row($request); list ($pgl, $version) = explode(" ", $version); return $version;',
	'validate_prefix' => function (&$value) {
		global $txt;

		$value = preg_replace('~[^A-Za-z0-9_\$]~', '', $value);

		// Is it reserved?
		if ($value == 'pg_')
			return $txt['error_db_prefix_reserved'];

		// Is the prefix numeric?
		if (preg_match('~^\d~', $value))
			return $txt['error_db_prefix_numeric'];

		return true;
	},
	'require_db_confirm' => true,
);