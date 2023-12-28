<?php
/**
 *
 * SPDX-License-Identifier: MIT
 */

$data = [
	'@context' => 'https://www.w3.org/ns/activitystreams'
	, 'id' => sprintf('https://%s/', $_SERVER['SERVER_NAME'])
	, 'type' => 'Person'
	, 'name' => 'Rivus Demo'
	, 'icon' => sprintf('https://%s/img/icon.png', $_SERVER['SERVER_NAME'])
	// , 'preferredUsername' => 'Rivus Demo'
	, 'summary' => 'I am a Rivus Demo Person'
	, 'inbox' =>    sprintf('https://%s/incoming', $_SERVER['SERVER_NAME'])
	, 'outgoing' => sprintf('https://%s/outgoing', $_SERVER['SERVER_NAME'])
	// "followers": "https://social.example/alyssa/followers/",
	// "following": "https://social.example/alyssa/following/",
	// "liked": "https://social.example/alyssa/liked/"
	// , 'provideClientKey' => ''
	// , 'signClientKey' => ''
	// , 'sharedInbox' => ''
];

$view_contact = 'Public';

echo '<main>';

printf('<h1>%s</h1>', $data['name']);

echo '<h2>Public Postings</h2>';

$sql = 'SELECT * FROM post_outgoing ORDER BY id DESC LIMIT 20 OFFSET 0';
$res = $dbc->fetchAll($sql);
// var_dump($res);
foreach ($res as $rec) {
	switch ($rec['type']) {
		// case 'Article':
		// case 'Audio':
		// case 'Document':
		// case 'Event':
		// case 'Image':
		case 'Note':
			// If i'm the logged in author then I should be able to delete this thing?
			echo '<section class="note">';
			echo _text_to_html($rec['source']);
			printf('<div class=""><a href="/feed/%s">/feed/%s</a></div>', $rec['id'], $rec['id']);
			echo '</section>';
			break;
		// case 'Page':
		// case 'Place':
		// case 'Profile':
		// case 'Relationship':
		// case 'Tombstone':
		// case 'Video':
		// 	break;
		default:
			printf('<section><h2>Unknown Type: %s</h2></section>', $rec['type']);
	}
}


echo '<pre>';
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo '</pre>';

echo '</main>';

echo '<script type="application/ld+json">';
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo '</script>';

/*
{
"@context": "https://www.w3.org/ns/activitystreams",
 "type": "Note",
 "to": ["https://chatty.example/ben/"],
 "attributedTo": "https://social.example/alyssa/",
 "content": "Say, did you finish reading that book I lent you?"}
 */
