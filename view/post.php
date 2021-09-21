<?php
/**
 *
 */

switch ($_POST['a']) {
	case 'activity-create':

		var_dump($_POST);
		var_dump($_FILES);
		// move_uploaded_file($_FILES['activity-file'], '');

		$rec = [
			'id' => \Edoceo\Radix\ULID::create()
			, 'link' => ''
			, 'type' => $_POST['activity-type']
			, 'name' => $_POST['Activity Post']
			, 'source' => json_encode([
				'text' => $_POST['activity-source-text']
				, 'link' => $_POST['activity-source-link']
				, 'file' => '',
			])
		];
		var_dump($rec);
		$res = $dbc->insert('post_outgoing', $rec);
		var_dump($res);


		break;


	case 'follow-create':

		echo '<pre>';

		$url = $_POST['follow-url'];
		$url = filter_var($url, FILTER_VALIDATE_URL);
		$actor_base = sprintf('https://%s', parse_url($url, PHP_URL_HOST));
		// var_dump($url);

		$req = __curl_init($url);
		$res = curl_exec($req);
		$inf = curl_getinfo($req);
		if (200 != $inf['http_code']) {
			__exit_text('Invalid Response [SVP-025]', 500);
		}

		// @todo use DOM
		$jld = preg_match('/<script type="application\/ld\+json">(.+?)<\/script>/ms', $res, $m) ? json_decode($m[1], true) : null;
		if (empty($jld)) {
			__exit_text('Invalid Response [SVP-031]', 500);
		}
		// var_dump($jld);

		// Get their KEY
		$url = sprintf('%s/.public', $actor_base);
		var_dump($url);

		$req = __curl_init($url);
		$res = curl_exec($req);
		$inf = curl_getinfo($req);
		if (200 != $inf['http_code']) {
			__exit_text('Invalid Response [SVP-041]', 500);
		}
		// var_dump($res); exit;
		$public_key1 = $res;
		printf("target-public-key: %s\n", $public_key1);
		$public_key1 = sodium_base642bin($public_key1, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

		// Now use my Secret Key
		$secret_key0 = sodium_crypto_box_secretkey($_ENV['site-key']);
		$public_key0 = sodium_crypto_box_publickey($_ENV['site-key']);
		$public_key0_b64 = sodium_bin2base64($public_key0, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

		// Crypted MEssage
		$twokey = sodium_crypto_box_keypair_from_secretkey_and_publickey($secret_key0, $public_key1);
		$message_plain = sprintf('I would like to Follow You from %s', $_SERVER['SERVER_NAME']);
		printf("message-plain-size: %d\n", strlen($message_plain));
		$message_nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
		$message_nonce_b64 = sodium_bin2base64($message_nonce, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

		$message_crypt = sodium_crypto_box($message_plain, $message_nonce, $twokey);
		printf("message-crypt-size: %d\n", strlen($message_crypt));

		$message_crypt_b64 = sodium_bin2base64($message_crypt, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
		printf("message-crypt-b64: %s %d\n", $message_crypt_b64, strlen($message_crypt_b64));

		// Now POST the Follow Message to Beta?
		$url = sprintf('%s/incoming', $actor_base);
		echo "send-to: {$url}\n";

		$req = __curl_init($url);
		curl_setopt($req, CURLOPT_HTTPHEADER, array(
			'accept: application/json'
			, 'content-type: application/json'
		));
		curl_setopt($req, CURLOPT_POSTFIELDS , json_encode([
			"@context" => "https://www.w3.org/ns/activitystreams"
			, "id" => sprintf("https://%s/activity/%s", $_SERVER['SERVER_NAME'], \Edoceo\Radix\ULID::create())
			, "type" => "Follow"
			, "actor" => sprintf("https://%s/", $_SERVER['SERVER_NAME']) // What? Me?
			, "object" => $actor_base // Who I want to Follow
			, "_salt_public" => $public_key0_b64
			, "_salt_nonce" => $message_nonce_b64
			, "_salt_content" => $message_crypt_b64
		]));

		$res = curl_exec($req);
		$inf = curl_getinfo($req);
		if (200 != $inf['http_code']) {
			// __exit_text('Invalid Response', 500);
		}

		echo "Response:\n{$res}\n###\n";

		echo '</pre>';

		break;
}


?>

<form enctype="multipart/form-data" method="post">
<h1>POST to your Feed</h1>

<select name="activity-type">
	<option value="">- select content type -</option>
	<option>Article</option>
	<option>Audio</option>
	<option>Document</option>
	<option>Event</option>
	<option>Image</option>
	<option>Note</option>
	<option>Page</option>
	<option>Place</option>
	<option>Video</option>
</select>

<div id="activity-text">
	<label>Activity Source <small>(Article, Document?, Event?, Note, Place?)</small></label>
	<textarea name="activity-source-text" style="width:100%;"></textarea>
</div>

<div id="activity-link">
	<label>Activity Link <small>(Audio?, Document?, Image?, Page, Video?)</small></label>
	<input name="activity-source-link" style="width:100%;" type="url">
</div>

<div id="activity-file">
	<label>Activity File <small>(Audio, Document, Image, Video)</small></label>
	<input name="activity-source-file" style="width:100%;" type="file">
</div>

<div id="publish-group">
	<h3>Publish Groups Here</h3>
	<div class="badge bg-success">#Public</div>
</div>


<div>
	<button name="a" type="submit" value="activity-create">Create</button>
</div>

</form>

<hr>

<form method="post">
<section>
	<h2>Follow Someone</h2>
	<input name="follow-url" style="width:100%;" type="url">
	<input name="CSRF" type="hidden" value="<?= $CSRF ?>">
	<button name="a" value="follow-create">Follow</button>
</section>
</form>
