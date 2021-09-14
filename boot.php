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
		$dbc->query('CREATE TABLE _saltfan (key PRIMARY KEY, val)');
		$dbc->query('CREATE TABLE post_incoming (id PRIMARY KEY, link, name, body, meta)');
		$dbc->query('CREATE TABLE post_outgoing (id PRIMARY KEY, link, name, body, meta)');
	}

	return $dbc;

}


/**
 *
 */
function _exit_html($body, $name='Saltfan')
{
	ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="application-name" content="Saltfan">
<meta name="theme-color" content="#0317a7">
<link rel="stylesheet" href="https://nobscss.com/nobs.css" crossorigin="anonymous">
<title><?= $name ?></title>
</head>
<body>
	<?= $body ?>
</body>
</html>
<?php
	$html = ob_get_clean();
	__exit_html($html);
}


function _text_to_html($t)
{
	// $pd = new ParseDownExtra();
	// return $pd->text($t);
	return $t;
}
