<?php
/**
 * Main Controller for Rivus
 *
 * SPDX-License-Identifier: MIT
 */

use Edoceo\Rivus\B64;

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
	$val = B64::encode($val);
	$dbc->query('INSERT INTO _rivus_config VALUES (:k, :v)', [
		':k' => 'site-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no SITE level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre><code>$val</code></pre>");
} else {
	$_ENV['site-key'] = B64::decode($chk);
	$_ENV['site-public-key-b64'] = B64::encode(sodium_crypto_box_publickey($_ENV['site-key']));
}

// User Key
// @deprecated?
$chk = $dbc->fetchOne('SELECT val FROM _rivus_config WHERE key = ?', [ 'user-ed25519-key-pair' ] );
if (empty($chk)) {
	$val = sodium_crypto_box_keypair();
	$val = B64::encode($val);
	$dbc->query('INSERT INTO _rivus_config VALUES (:k, :v)', [
		':k' => 'user-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no USER level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre>$val</pre>");
} else {
	$_ENV['user-key'] = B64::decode($chk);
}


// Configure Environment
$cfg = $dbc->fetchAll("SELECT key, val FROM _rivus_config WHERE key LIKE 'site-%'");

$hrr = new \Edoceo\Radix\HTTP\Router();
$hrr->get('/', 'Edoceo\Rivus\Home');
$hrr->get('/home', 'Edoceo\Rivus\Home');

$hrr->get('/auth/google', function() {

	session_start();

	header('content-type: text/plain');

	var_dump($_SERVER);

	var_dump($_GET);

	$cfg = yaml_parse_file(APP_ROOT . 'etc/config.yaml');
	$cfg = $cfg['youtube']['edoceo'];

	$provider = new Google([
		'clientId'     => $cfg['oauth_client_id'],
		'clientSecret' => $cfg['oauth_client_secret'],
		'redirectUri'  => $cfg['oauth_redirect'],
	]);

	if (!isset($_GET['code'])) {
		// IMPORTANT: 'access_type' => 'offline' and 'prompt' => 'consent'
		// are required to get a Refresh Token.
		$authUrl = $provider->getAuthorizationUrl([
			'scope' => ['https://www.googleapis.com/auth/youtube.upload'],
			'access_type' => 'offline',
			'prompt' => 'consent'
		]);
		$_SESSION['oauth2state'] = $provider->getState();
		header('Location: ' . $authUrl);
		exit;

	} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
		exit('Invalid state');
	} else {

		$token = $provider->getAccessToken('authorization_code', [
			'code' => $_GET['code']
		]);

		// This is what you need to save in your database or .env file!
		echo "Refresh Token: " . $token->getRefreshToken();

		// var_dump($token);

	}

	exit(0);
});

// Feed Views
$hrr->get('/feed', function() { __exit_text('Show My Feed, My Self-Published Outgoing, HTML'); });
$hrr->get('/feed/atom.xml', function() { __exit_text('Show My Feed, My Self-Published Outgoing, ATOM'); });
$hrr->get('/feed/feed.json', function() { __exit_text('Show My Feed, My Self-Published Outgoing, JSON'); });
$hrr->get('/feed/rss.xml', function() { __exit_text('Show My Feed, My Self-Published Outgoing, RSS'); });

$hrr->get('/auth', 'Edoceo\Rivus\Auth\Main');
// $hrr->add('/auth/open', 'Edoceo\Rivus\Auth\Main');
// $hrr->add('/auth/open', 'Edoceo\Rivus\Auth\Main', 'POST');
// $hrr->add('/auth/token', 'Edoceo\Rivus\Auth\Main');

// $hrr->add('/cal', 'Edoceo\Rivus\Calendar\Pub');

$hrr->get('/incoming', 'Edoceo\Rivus\Incoming');
$hrr->get('/outgoing', 'Edoceo\Rivus\Outgoing');

$hrr->get('/config', 'Edoceo\Rivus\Controller\Config');
$hrr->post('/config', 'Edoceo\Rivus\Controller\Config:post');

$hrr->get('/ping', function($REQ) {
	ob_start();
	require_once(APP_ROOT . '/view/ping.php');
	_exit_html(ob_get_clean());
});
$hrr->get('/post', function($REQ) {
	ob_start();
	require_once(APP_ROOT . '/view/post.php');
	_exit_html(ob_get_clean());
});
$hrr->get('/publish', function($REQ) {
	ob_start();
	require_once(APP_ROOT . '/view/publish.php');
	_exit_html(ob_get_clean());
});

$hrr->get('/share', function($REQ) {
	ob_start();
	require_once(APP_ROOT . '/view/share-manual.php');
	_exit_html(ob_get_clean());
});
$hrr->post('/share/draft', function($REQ) {
	// ob_start();
	// _exit_html(ob_get_clean());
	$output_data = file_get_contents('php://input');
	$output_data = trim($output_data);
	if (empty($output_data)) {
		__exit_text('No Input', 400);
	}

	$output_file = sprintf('%s/var/draft/%s.txt', APP_ROOT, \Edoceo\Radix\ULID::create());
	$output_path = dirname($output_file);
	if ( ! is_dir($output_path)) {
		mkdir($output_path, 0755, true);
	}
	$x = file_put_contents($output_file, $output_data);
	__exit_text($x);
});

// Maybe this should re-direct? Or go away?
$hrr->get('/.public', function($REQ) {
	__exit_text($_ENV['site-public-key-b64']);
});

$hrr->get('/.well-known/rivus/pk', function($REQ) {
	__exit_text($_ENV['site-public-key-b64']);
});

// ActivityPub Stuff
$hrr->get('/.well-known/webfinger', function($REQ) { __exit_text('ActivityPub Finger'); });
$hrr->get('/feed/outgoing.xml', function($REQ) { __exit_text('ActivityPub Finger'); });

$REQ = new \Edoceo\Radix\HTTP\Request();
$REQ->setAttribute('dbc', $dbc);
$RES = $hrr->handle($REQ);

if (empty($RES)) {
	throw new \Exception('Server Error [ERA-132]', 500);
}
