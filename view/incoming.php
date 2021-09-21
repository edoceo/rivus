<?php
/**
 * Saltfan Incoming
 */

switch ($_SERVER['REQUEST_METHOD']) {
	case 'DELETE':
		// Me removing my stuff?
		break;
	case 'GET':
		// Me, Reading my Inbox?
		break;
	case 'POST':

		// Folks POST to me?
		$source = file_get_contents('php://input');
		$source_data = json_decode($source, true);

		// I'm the Target on Incoming
		$target_secret_key = sodium_crypto_box_secretkey($_ENV['site-key']);

		$source_public_key_b64 = $source_data['_salt_public'];
		$source_public_key = sodium_base642bin($source_public_key_b64, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);


		$message_crypt_b64 = $source_data['_salt_content'];
		$message_crypt = sodium_base642bin($message_crypt_b64, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
		printf("incoming-message-crypt-size: %d\n", strlen($message_crypt));

		$message_nonce_b64 = $source_data['_salt_nonce'];
		$message_nonce = sodium_base642bin($message_nonce_b64, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

		// $decrypt_pair
		$twokey = sodium_crypto_box_keypair_from_secretkey_and_publickey($target_secret_key, $source_public_key);
		$message_plain = sodium_crypto_box_open($message_crypt, $message_nonce, $twokey);


		$dbc = _dbc($_ENV['host']);
		$dbc->insert('post_incoming', [
			'id' => \Edoceo\Radix\ULID::create()
			, 'link' => ''
			, 'type' => $source_data['type']
			, 'name' => sprintf('Follow from %s', $source_data['actor'])
			, 'source' => $source
			, 'output' => $message_plain
		]);

		__exit_text([
			'data' => [
				'_SERVER' => $_SERVER,
				'source' => $source_data,
				'target' => $target_secret_key,
				'output' => $message_plain
			],
			'meta' => [ 'detail' => 'Seems Legit' ]
		]);

}

// echo '<h1>Incoming</h1>';

// echo '<pre>';
// var_dump($_COOKIE);
// var_dump($_GET);
// var_dump($_POST);
// echo '</pre>';
