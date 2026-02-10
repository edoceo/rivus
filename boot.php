<?php
/**
 * Rivus Bootstrap
 *
 * SPDX-License-Identifier: MIT
 */

use ActivityPhp\Type\TypeConfiguration;

require_once(__DIR__ . '/vendor/autoload.php');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/**
 * Error Handler
 */
set_error_handler(function($num=null, $str=null, $file=null, $line=null, $ctx=null) {

	while (ob_get_level()) {
		ob_end_clean();
	}

	$file = str_replace(__DIR__, '', $file);

	$text = [];
	$text[] = sprintf('Error %d / %s', $num, $str);
	if ( ! empty($file)) {
		$text[] = sprintf('File: %s#%d' , $file, $line);
	}

	$text = implode("\n", $text);

	__exit_text($text, 500);

}, E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

/**
 * Exception Handler
 */
set_exception_handler(function($ex) {

	while (ob_get_level()) {
		ob_end_clean();
	}

	$file = $ex->getFile();
	$file = str_replace(__DIR__, '', $file);

	$text = [];
	$text[] = 'Exception: ' . get_class($ex);
	$text[] = $ex->getMessage();
	$text[] = sprintf('File: %s#%d', $file, $ex->getLine());

	$text = implode("\n", $text);

	__exit_text($text, 500);

});

// Configure ActivtyPub libs
\ActivityPhp\Type\TypeConfiguration::set('undefined_properties', 'include');

define('APP_ROOT', __DIR__);
// define('__PHP_USERAGENT', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');

/**
 *
 */
function _dbc(string $host='')
{
	if (empty($host)) {
		$host = $_SERVER['SERVER_NAME'];
	}

	$sql_file = sprintf('%s/var/%s/database.sqlite', APP_ROOT, $host);
	$sql_good = is_file($sql_file);

	$dbc = new \Edoceo\Radix\DB\SQL(sprintf('sqlite:%s', $sql_file));
	if ( ! $sql_good) {
		$dbc->query('CREATE TABLE IF NOT EXISTS _keylist (key PRIMARY KEY, name)');
		$dbc->query('CREATE TABLE IF NOT EXISTS _rivus_config (key PRIMARY KEY, val)');
		$dbc->query('CREATE TABLE IF NOT EXISTS post_incoming (id PRIMARY KEY, link, type, name, source, output, meta)');
		$dbc->query('CREATE TABLE IF NOT EXISTS post_outgoing (id PRIMARY KEY, link, type, name, source, output, meta)');
	}

	$dbc->query('CREATE TABLE IF NOT EXISTS _keylist (key PRIMARY KEY, name)');
	$dbc->query('CREATE TABLE IF NOT EXISTS "feed_source" (id PRIMARY KEY, created_at NUMERIC, checked_at NUMERIC, updated_at NUMERIC, stat NUMERIC, link TEXT, type TEXT, name TEXT, source TEXT, output TEXT, meta TEXT)');

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
	static $converter;

	if (empty($converter)) {
		$converter = new League\CommonMark\CommonMarkConverter([
			'html_input' => 'strip',
			'allow_unsafe_links' => false,
		]);
	}

	return $converter->convert($t);

}
