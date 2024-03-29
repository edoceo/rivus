<?php
/**
 *
 */

use Edoceo\Rivus\B64;

?>

<h1>Ping</h1>
<p>Check this server status</p>


<h2>Pong <small>ping reply</small></h2>

<pre>
site:            <?= $_ENV['host'] ?>
<?php
// var_dump($_ENV);
?>

<?php
/**
 * Show Crypto Box Keypairs
 */
printf("site-public-key: <a href=\"/.well-known/site-public-key\">%s</a>\n",
	B64::encode(sodium_crypto_box_publickey($_ENV['site-key'])));


//printf("SITE-KEY-Secret: %s\n",
//	B64::encode(sodium_crypto_box_secretkey($_ENV['site-key'])));

echo "\n";

printf("user-public-key: <a href=\"/.well-known/user-public-key\">%s</a>\n",
	B64::encode(sodium_crypto_box_publickey($_ENV['user-key'])));


/**
 * Key Exchange Keypairs
 * Why would we need to make these?
 */

/*
$twokey = sodium_crypto_kx_keypair();
$public = sodium_crypto_kx_publickey($twokey);
$secret = sodium_crypto_kx_secretkey($twokey);
printf("Key Exchange Keys:\n\n  pair:   %s[%d]\n  secret: %s[%d]\n  public: %s[%d]"
	, sodium_bin2base64($twokey, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
	, strlen($twokey)
	, sodium_bin2base64($secret, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
	, strlen($secret)
	, sodium_bin2base64($public, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
	, strlen($public)
);
*/


/**
 * Signing Key-Pair
 * Why would we need to make these?
 */
/*
$twokey = sodium_crypto_sign_keypair();
$public = sodium_crypto_sign_publickey($twokey);
$secret = sodium_crypto_sign_secretkey($twokey);
printf("## Signing Keys\n\n  pair:   %s[%d]\n  secret: %s\n  public: %s"
	, sodium_bin2base64($twokey, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
	, strlen($twokey)
	, sodium_bin2base64($secret, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
	, sodium_bin2base64($public, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING));
*/
?>
</pre>
