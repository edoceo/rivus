<?php
/**
 * Rivus Bootstrap
 *
 * SPDX-License-Identifier: MIT
 */


use ActivityPhp\Type\TypeConfiguration;

define('APP_ROOT', __DIR__);
require_once(__DIR__ . '/vendor/autoload.php');

error_reporting(E_ALL & ~E_NOTICE);

// Configure ActivtyPub libs
TypeConfiguration::set('undefined_properties', 'include');


/**
 *
 */
function _dbc($host)
{
	$sql_file = sprintf('%s/var/%s/database.sqlite', APP_ROOT, $host);
	$sql_good = is_file($sql_file);

	$dbc = new \Edoceo\Radix\DB\SQL(sprintf('sqlite:%s', $sql_file));
	if ( ! $sql_good) {
		$dbc->query('CREATE TABLE _keylist (key PRIMARY KEY, name)');
		$dbc->query('CREATE TABLE _rivus_config (key PRIMARY KEY, val)');
		$dbc->query('CREATE TABLE post_incoming (id PRIMARY KEY, link, type, name, source, output, meta)');
		$dbc->query('CREATE TABLE post_outgoing (id PRIMARY KEY, link, type, name, source, output, meta)');
	}

	$dbc->query('CREATE TABLE IF NOT EXISTS _keylist (key PRIMARY KEY, name)');

	return $dbc;

}


/**
 * Exit w/some HTML Output
 */
function _exit_html($body, $name='Rivus', $code=200)
{
	$view = new Edoceo\Rivus\Output\HTML($body);
	$view->setPageTitle($name);
	__exit_html($view->render(), $code);
}

/**
 * Turn Markdown into HTML
 */
function _text_to_html($t)
{
	$pde = new ParsedownExtra();
	return $pde->text($t);
}
