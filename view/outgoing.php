<?php
/**
 * Rivus Outgoing
 *
 * SPDX-License-Identifier: MIT
 */

$path = array_shift($path_list);
switch ($path) {
	case '':
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				_draw_post_recent();
		}
		break;
	case 'create':
		_draw_post_create();
}

/**
 *
 */
function _draw_post_create()
{
	require_once(APP_ROOT . '/app/outgoing/create.php');
}


/**
 *
 */
function _draw_post_recent()
{
	global $dbc;

	echo '<h1>Outgoing</h1>';

	echo '<a class="btn btn-primary" href="/outgoing/create">Create Post</a>';

	$sql = 'SELECT * FROM post_outgoing ORDER BY id DESC LIMIT 20 OFFSET 0';
	$res = $dbc->fetchAll($sql);
	// var_dump($res);
	echo '<h2>Recent Posts</h2>';
	foreach ($res as $rec) {
		echo '<div>';
		echo '<pre><code>';
		echo __json_encode($rec, JSON_PRETTY_PRINT);
		echo '</code></pre>';
		echo '</div>';
	}

}
