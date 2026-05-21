<?php
/**
 *
 */

namespace Edoceo\Rivus\Controller;

class Config
{
	/**
	 *
	 */
	function __invoke($REQ)
	{
		ob_start();
		require_once(APP_ROOT . '/view/config/main.php');
		_exit_html(ob_get_clean());

	}

	/**
	 *
	 */
	function post($REQ)
	{
		switch ($_POST['a']) {
		case 'feed-source-create':
			$this->addFeed($_POST['feed-source']);
			break;
		}
		Radix::redirect('/config');
	}

	/**
	 *
	 */
	function addFeedSource($url)
	{
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
				$this->addFeedSourceOPML($feed_import);
				break;
			default:
				header('content-type: text/plain');

				// Maybe a Regular Feed
				// Add to the feed_source table?
				echo "Feed Import is Odd\n";
				var_dump($info);
				var_dump($feed_import);


				$chk = $dbc->fetchOne('SELECT id FROM feed_source WHERE link = :l0', [ ':l0' => $url ]);
				if (empty($chk)) {
					$rec = [];
					$rec['id'] = md5($url);
					$rec['created_at'] = time();
					$rec['type'] = $info['mime'];
					$rec['link'] = $info['url'];
					$rec['name'] = $info['url'];
					$rec['source'] = json_encode($info);
					// $rec['id'] = md5($rec['source']);
					var_dump($rec);
					$dbc->insert('feed_source', $rec);
				}
				break;
		}

	}

	/**
	 *
	 */
	function addFeedSourceOPML($feed)
	{
		// XML
		$item_list = $feed->getItems();
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

	}
}
