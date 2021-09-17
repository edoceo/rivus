<?php
/**
 *
 */

// https://www.w3.org/TR/activitystreams-vocabulary/#object-types
$object_type_list = [
	'Note'
	, 'Article'
	, 'Audio'
	, 'Video'
];

?>

/*
Document
Event
Image
Page
Place
Profile
Relationship
Tombstone
*/

<?php

$dbc = _dbc($_SERVER['SERVER_NAME']);
$dbc->insert('post_outgoing', [
	'id' => \Edoceo\Radix\ULID::create()
	, 'type' => 'Note'
	, 'source' => "# Outoing Note\n\nThis is an outgoing public note"
	, 'output' => ''
]);
