<?php
/**
 * Main Controller for Saltfan
 */

require_once(sprintf('%s/boot.php', dirname(__DIR__)));

header('cache-control: no-store,max-age=0');
header('content-type: text/plain');


// Host
$host = $_SERVER['SERVER_NAME'];
$host_path = sprintf('%s/var/%s', APP_ROOT, rawurlencode($host));
if ( ! is_dir($host_path)) {
	mkdir($host_path, 0775, true);
	_exit_html('<h1>This Host is NOT configured</h1>');
}

$dbc = _dbc($host);
$chk = $dbc->fetchOne('SELECT val FROM _saltfan WHERE key = ?', [ 'site-ed25519-key-pair' ] );
if (empty($chk)) {
	$val = sodium_crypto_box_keypair();
	$val = sodium_bin2base64($val, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
	$dbc->query('INSERT INTO _saltfan VALUES (:k, :v)', [
		':k' => 'site-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no SITE level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre>$val</pre>");
}

$chk = $dbc->fetchOne('SELECT val FROM _saltfan WHERE key = ?', [ 'user-ed25519-key-pair' ] );
if (empty($chk)) {
	$val = sodium_crypto_box_keypair();
	$val = sodium_bin2base64($val, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
	$dbc->query('INSERT INTO _saltfan VALUES (:k, :v)', [
		':k' => 'user-ed25519-key-pair',
		':v' => $val,
	]);
	_exit_html("<h1>You had no USER level keypair, they were created</h1><p>Copy this value and do NOT lose it</p><pre>$val</pre>");
}


// Path
$path0 = $_SERVER['REQUEST_URI'];
$path0 = strtok($path0, '?');
// stanatize double slashed to remove them all
$path1 = $path0;
$path1 = preg_replace('/\/+/', '/', $path1);
if ($path0 != $path1) {
	_exit_html("<h1>Should Redirect '$path0' =&gt; '$path1'</h1>");
}
// $path1 = substr($path1, 0, strpos($path1, '/', 1));
// var_dump($path1); exit;
$path1 = ltrim($path0, './');
$path_list = explode('/', $path1);
// var_dump($path_list); exit;

// Switch base from ActivityPub Object Type?
switch (sprintf('/%s', $path_list[0])) {
case '':
case '/': // home

	ob_start();
	require_once(APP_ROOT . '/view/home.php');
	_exit_html(ob_get_clean());

	// $text = file_get_contents(sprintf('%s/home.md', $host_path));
	// $html = _text_to_html($text);
	// // $feed = $dbc->fetchAll('SELECT * FROM ')
	// _exit_html($html);

case '/incoming':
	require_once(APP_ROOT . '/view/incoming.php');
	break;
case '/outgoing':
	require_once(APP_ROOT . '/view/incoming.php');
	break;
case '/publish':
	require_once(APP_ROOT . '/view/publish.php');
	break;
}

print_r(get_included_files());
print_r(memory_get_peak_usage(true));

exit(0);
