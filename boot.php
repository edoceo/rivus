<?php
/**
 * Saltfan Bootstrap
 */

define('APP_ROOT', __DIR__);
require_once(__DIR__ . '/vendor/autoload.php');

error_reporting(E_ALL & ~E_NOTICE);

function _dbc($host)
{
	$sql_file = sprintf('%s/var/%s/database.sqlite', APP_ROOT, $host);
	$sql_good = is_file($sql_file);

	$dbc = new \Edoceo\Radix\DB\SQL(sprintf('sqlite:%s', $sql_file));
	if ( ! $sql_good) {
		$dbc->query('CREATE TABLE _keylist (key PRIMARY KEY, name)');
		$dbc->query('CREATE TABLE _saltfan (key PRIMARY KEY, val)');
		$dbc->query('CREATE TABLE post_incoming (id PRIMARY KEY, link, type, name, source, output, meta)');
		$dbc->query('CREATE TABLE post_outgoing (id PRIMARY KEY, link, type, name, source, output, meta)');
	}

	$dbc->query('CREATE TABLE IF NOT EXISTS _keylist (key PRIMARY KEY, name)');

	return $dbc;

}


/**
 *
 */
function _exit_html($body, $name='Saltfan', $code=200)
{
	$view = new App\Output\HTML($body);
	$view->setPageTitle($name);
	__exit_html($view->render(), $code);
}


function _text_to_html($t)
{
	$pde = new ParsedownExtra();
	return $pde->text($t);
}
