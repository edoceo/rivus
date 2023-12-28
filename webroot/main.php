<?php
/**
 * Main Controller for Rivus
 *
 * SPDX-License-Identifier: MIT
 */

require_once(sprintf('%s/boot.php', dirname(__DIR__)));

// Host
$host = $_SERVER['SERVER_NAME'];
$host = strtolower($host);
$_ENV['host'] = $host;

$host_database = sprintf('%s/var/%s.sqlite', APP_ROOT, rawurlencode($host));
if ( ! is_file($host_database)) {
	_exit_html('<h1>This Site is NOT configured, the database must be created [SFM-017]</h1>', 'Error: 501', 501);
}

// Path
$path0 = $_SERVER['REQUEST_URI'];
$path0 = strtok($path0, '?');
$path0 = preg_replace('/[^\w\/\-\.]+/', '', $path0);
$path0 = trim($path0, '/. ');

$_ENV['path'] = $path0;

$path_list = explode('/', $_ENV['path']);

// Check my database
$dbc = _dbc($host_database);

// Site Key
$chk = $dbc->fetchOne('SELECT val FROM _rivus_config WHERE key = ?', [ 'site-ed25519-key-pair' ] );
if (empty($chk)) {
	// One string containing both the X25519 secret key and corresponding X25519 public key.
	$val = sodium_crypto_box_keypair();
	$val = sodium_bin2base64($val, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
	$dbc->query('INSERT INTO _rivus_config VALUES (:k, :v)', [
		':k' => 'site-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no SITE level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre><code>$val</code></pre>");
} else {
	$_ENV['site-key'] = sodium_base642bin($chk, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
	$_ENV['site-public-key-b64'] = sodium_bin2base64(sodium_crypto_box_publickey($_ENV['site-key']), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
}


// User Key
$chk = $dbc->fetchOne('SELECT val FROM _rivus_config WHERE key = ?', [ 'user-ed25519-key-pair' ] );
if (empty($chk)) {
	$val = sodium_crypto_box_keypair();
	$val = sodium_bin2base64($val, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
	$dbc->query('INSERT INTO _rivus_config VALUES (:k, :v)', [
		':k' => 'user-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no USER level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre>$val</pre>");
} else {
	$_ENV['user-key'] = sodium_base642bin($chk, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
}


// Configure Environment
$cfg = $dbc->fetchAll("SELECT key, val FROM _rivus_config WHERE key LIKE 'site-%'");

// Switch base from ActivityPub Object Type?
switch (sprintf('/%s', $path_list[0])) {
case '':
case '/': // home
case '/home':

	ob_start();
	require_once(APP_ROOT . '/view/home.php');
	_exit_html(ob_get_clean());

	// $text = file_get_contents(sprintf('%s/home.md', $host_path));
	// $html = _text_to_html($text);
	// // $feed = $dbc->fetchAll('SELECT * FROM ')
	// _exit_html($html);

case '/config':
	ob_start();
	require_once(APP_ROOT . '/app/config/main.php');
	_exit_html(ob_get_clean());
	break;
case '/ping':
	ob_start();
	require_once(APP_ROOT . '/view/ping.php');
	_exit_html(ob_get_clean());
case '/post':
	ob_start();
	require_once(APP_ROOT . '/view/post.php');
	_exit_html(ob_get_clean());
case '/incoming':
	ob_start();
	require_once(APP_ROOT . '/app/incoming/main.php');
	_exit_html(ob_get_clean());
	break;
case '/outgoing':
	// https://www.w3.org/TR/activitypub/#public-addressing
	ob_start();
	require_once(APP_ROOT . '/view/outgoing.php');
	_exit_html(ob_get_clean());
	break;
case '/publish':
	ob_start();
	require_once(APP_ROOT . '/view/publish.php');
	_exit_html(ob_get_clean());
	break;
// Rivus Code Here
case '/.public':
	__exit_text($_ENV['site-public-key-b64']);
}

_exit_html('<h1>Not Found</h1>', 'Not Found: 404', 404);
