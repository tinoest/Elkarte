<?php

/**
 * This file has all the main functions in it that set up the database connection
 * and initializes the appropriate adapters.
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 2.0 dev
 *
 */

/**
 * Initialize database classes and connection.
 *
 * @param string $db_server
 * @param string $db_name
 * @param string $db_user
 * @param string $db_passwd
 * @param string $db_prefix
 * @param mixed[] $db_options
 * @param string $db_type
 *
 * @return \ElkArte\DatabaseInterface
 * @throws \Exception
 */
function elk_db_initiate($db_server, $db_name, $db_user, $db_passwd, $db_prefix, $db_options = array(), $db_type = 'mysql')
{
	return database(false);
}

/**
 * Retrieve existing instance of the active database class.
 *
 * @param bool $fatal - Stop the execution or throw an \Exception
 *
 * @return \ElkArte\DatabaseInterface
 * @throws \Exception
 */
function database($fatal = true)
{
	static $db = null;

	if ($db === null)
	{
		global $db_persist, $db_server, $db_user, $db_passwd, $db_port;
		global $db_type, $db_name, $db_prefix, $mysql_set_mode;

		$db_options = [
			'persist' => $db_persist,
			'select_db' => true,
			'port' => $db_port,
			'mysql_set_mode' => (bool) ($mysql_set_mode ?? false)
		];
		$db_type = strtolower($db_type);
		$db_type = $db_type === 'mysql' ? 'mysqli' : $db_type;
		$class = '\\ElkArte\\Database\\' . ucfirst($db_type) . '\\Connection';
		try
		{
			$db = $class::initiate($db_server, $db_name, $db_user, $db_passwd, $db_prefix, $db_options);
		}
		catch (\Exception $e)
		{
			if ($fatal === true)
			{
				\ElkArte\Errors\Errors::instance()->display_db_error($e->getMessage());
			}
			else
			{
				throw $e;
			}
		}
		
	}
	return $db;
}

/**
 * This function retrieves an existing instance of DbTable
 * and returns it.
 *
 * @param object|null $db - A database object (e.g. Database_MySQL or Database_PostgreSQL)
 * @param bool $fatal - Stop the execution or throw an \Exception
 *
 * @return \ElkArte\DatabaseInterface
 * @throws \Exception
 */
function db_table($db = null, $fatal = false)
{
	global $db_prefix, $db_type;
	static $db_table = null;

	if ($db_table === null)
	{
		$db = database();
		$db_type = strtolower($db_type);
		$db_type = $db_type === 'mysql' ? 'mysqli' : $db_type;
		$class = '\\ElkArte\\Database\\' . ucfirst($db_type) . '\\Table';
		try
		{
			$db_table = new $class($db, $db_prefix);
		}
		catch (\Exception $e)
		{
			if ($fatal === true)
			{
				\ElkArte\Errors\Errors::instance()->display_db_error($e->getMessage());
			}
			else
			{
				throw $e;
			}
		}
	}

	return $db_table;
}

/**
 * This function returns an instance of DbSearch,
 * specifically designed for database utilities related to search.
 *
 * @return DbSearch
 *
 */
function db_search()
{
	static $db_search = null;

	if ($db_search === null)
	{
		$db = database();
		$db_type = strtolower($db_type);
		$db_type = $db_type === 'mysql' ? 'mysqli' : $db_type;
		$class = '\\ElkArte\\Database\\' . ucfirst($db_type) . '\\Search';
		try
		{
			$db_search = new $class($db);
		}
		catch (\Exception $e)
		{
			if ($fatal === true)
			{
				\ElkArte\Errors\Errors::instance()->display_db_error($e->getMessage());
			}
			else
			{
				throw $e;
			}
		}
	}

	return $db_search;
}
