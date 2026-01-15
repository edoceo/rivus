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

$host_path = sprintf('%s/var/%s', APP_ROOT, rawurlencode($host));
if ( ! is_dir($host_path)) {
	_exit_html('<h1>This Host is NOT configured [SFM-017]</h1>', 'Error: 501', 501);
}

// Check my database
$dbc = _dbc($host);

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
// @deprecated?
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

$hrr = new \Edoceo\Radix\HTTP\Router();
$hrr->add('/', function() { __exit_text('/'); });
$hrr->add('/home', function() {

	// __exit_text($_SERVER['SERVER_NAME']);

	ob_start();
	require_once(APP_ROOT . '/view/home.php');
	_exit_html(ob_get_clean());

	// $text = file_get_contents(sprintf('%s/home.md', $host_path));
	// $html = _text_to_html($text);
	// // $feed = $dbc->fetchAll('SELECT * FROM ')
	// _exit_html($html);

});

// Feed Views
$hrr->add('/feed', function() { __exit_text('Show My Feed, My Self-Published Outgoing'); });
$hrr->add('/feed/atom.xml', function() { __exit_text('Show My Feed, My Self-Published Outgoing'); });
$hrr->add('/feed/feed.json', function() { __exit_text('Show My Feed, My Self-Published Outgoing'); });
$hrr->add('/feed/rss.xml', function() { __exit_text('Show My Feed, My Self-Published Outgoing'); });

$hrr->add('/auth', 'Edoceo\Rivus\Auth\Main');
// $hrr->add('/auth/open', 'Edoceo\Rivus\Auth\Main');
// $hrr->add('/auth/open', 'Edoceo\Rivus\Auth\Main', 'POST');
// $hrr->add('/auth/token', 'Edoceo\Rivus\Auth\Main');

// $hrr->add('/cal', 'Edoceo\Rivus\Calendar\Pub');

$hrr->add('/incoming', 'Edoceo\Rivus\Incoming');
$hrr->add('/outgoing', 'Edoceo\Rivus\Outgoing');

$hrr->add('/config', function() {
	ob_start();
	require_once(APP_ROOT . '/app/config/main.php');
	_exit_html(ob_get_clean());
});
$hrr->add('/ping', function() {
	ob_start();
	require_once(APP_ROOT . '/view/ping.php');
	_exit_html(ob_get_clean());
});
$hrr->add('/post', function() {
	ob_start();
	require_once(APP_ROOT . '/view/post.php');
	_exit_html(ob_get_clean());
});
$hrr->add('/publish', function() {
	ob_start();
	require_once(APP_ROOT . '/view/publish.php');
	_exit_html(ob_get_clean());
});

$hrr->add('/.public', function() {
	__exit_text($_ENV['site-public-key-b64']);
});
$hrr->add('/.well-known', function() {

	switch ($path_list[1]) {
		case '':
			__exit_text('.well-known');
			break;
		case 'site-public-key':
			__exit_text($_ENV['site-public-key-b64']);
			break;
		case 'user-public-key':
			__exit_text($_ENV['user-public-key-b64']);
			break;
	}
	// return \Rivus\Controller\WellKnown($path_list);

});

// $hrr->add('/email', 'Edoceo\Rivus\Email\Main');
// $hrr->add('/email', 'Edoceo\Rivus\Email\Main', 'POST');
// $hrr->add('/pitch', 'Edoceo\Rivus\Pitch\Main');
// $hrr->add('/pitch/video', 'Edoceo\Rivus\Pitch\Main');
// $hrr->add('/program', 'Edoceo\Rivus\Program\Main');
// $hrr->add('/program/{program_code}', 'Edoceo\Rivus\Program\Main');
// $hrr->add('/timer', 'Edoceo\Rivus\Timer\Main');
// $hrr->add('/vote', 'Edoceo\Rivus\Vote\Main');

$REQ = new \Edoceo\Radix\HTTP\Request();
$RES = $hrr->handle($REQ);


exit;

_exit_html('<h1>Not Found</h1>', 'Not Found: 404', 404);
