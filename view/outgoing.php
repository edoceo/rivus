<?php
/**
 * Saltfan Outgoing
 */

echo '<h1>Outgoing</h1>';


echo '<pre>';
var_dump($_COOKIE);
var_dump($_GET);
var_dump($_POST);
echo '</pre>';


$sql = 'SELECT * FROM post_outgoing ORDER BY id LIMIT 20 OFFSET 0';
$res = $dbc->fetchAll($sql);
var_dump($res);
