<?php
/**
 * Configuration Editor
 *
 * SPDX-License-Identifier: MIT
 */

use Edoceo\Radix;

global $dbc;

switch ($_POST['a']) {
	case 'feed-source-create':

		$url = $_POST['feed-source'];
		if ( ! preg_match('/^http/', $url)) {
			echo '<div class="alert alert-danger">Invalid URL</div>';
			return null;
		}

		$url = filter_var($url, FILTER_VALIDATE_URL);

		$feed_import = new \Edoceo\Rivus\Feed\Reader($url);
		$feed_import->load();
		$info = $feed_import->getInfo();

		switch ($info['mime']) {
			case 'text/x-opml':
				// XML
				$item_list = $feed_import->getItems();
				foreach ($item_list as $src) {
					$chk = $dbc->fetchOne('SELECT id FROM feed_source WHERE link = :l0', [ ':l0' => $src['xmlUrl'] ]);
					if (empty($chk)) {
						$rec = [];
						$rec['id'] = md5($src['xmlUrl']);
						$rec['created_at'] = time();
						$rec['type'] = $src['type'];
						$rec['link'] = $src['xmlUrl'];
						$rec['name'] = $src['title'];
						$rec['source'] = json_encode($src);
						// $rec['id'] = md5($rec['source']);
						$dbc->insert('feed_source', $rec);
					}
				}
				break;
			default:
				// Maybe a Regular Feed
				// Add to the feed_source table?
				echo "Feed Import is Odd\n";
				var_dump($feed_import);
		}

		Radix::redirect('/config');

		break;

}



?>

<section>
<p>This form can take Atom, JSON, OPML or RSS links</p>
<form method="post">
	<input name="feed-source" type="url">
	<button name="a" type="submit" value="feed-source-create">Add Feed</button>
</form>
</section>

<hr>

<?php




$sql = <<<SQL
SELECT *
FROM feed_source
ORDER BY checked_at, stat, name
SQL;

$feed_list = $dbc->fetchAll($sql);

foreach ($feed_list as $feed) {
	echo '<pre>';
	echo __h(json_encode($feed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	echo '</pre>';
	echo sprintf('<a class="btn" href="/incoming/pull?feed=%s">Pull this Feed</a>', $feed['id']);
	echo '<hr>';
}
