<?php
/**
 * OPML Reader
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed\Reader;

class OPML extends \Edoceo\Rivus\Feed\Reader
{
	/**
	 *
	 */
	function __construct(string $source)
	{
		$this->data = $source;
		// $this->parse();
	}

	/**
	 *
	 */
	function getItems()
	{
		$xml = new \SimpleXMLElement($this->data);

		// Insert this as Feed-Source?
		$ret = [];

		foreach ($xml->body->outline->outline as $src) {
			// var_dump($src);
			$ret[] = $src;
		// 			// echo $src->getName();
		// 			// echo $src;
		// 			// echo "\n";

		// 			$rec = [];
		// 			$rec['id'] = md5($src['xmlUrl']);
		// 			$rec['created_at'] = time();
		// 			$rec['type'] = $src['type'];
		// 			$rec['link'] = $src['xmlUrl'];
		// 			$rec['name'] = $src['title'];
		// 			$rec['source'] = json_encode($src);
		// 			// $rec['id'] = md5($rec['source']);

		// 			$dbc->insert('feed_source', $rec);

		}

		return $ret;

		// 		break;

		// 	case 301:
		// 		echo "See Other: {$inf['redirect_url']}\n";
		// 		exit(1);
		// }

	}

}
