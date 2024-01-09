<?php
/**
 * RSS Feed Reader
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed\Reader;

class RSS extends XML
{
	protected $data;

	protected $mime;

	protected $type;

	protected $url;

	/**
	 *
	 */
	function getItems()
	{
		$ret = [];

		// Spin Each Channel
		foreach ($this->xml->channel->item as $src) {

			$rec = [];
			$rec['id'] = md5(json_encode($src));
			$rec['created_at'] = strtotime($src->pubDate);
			if ($rec['created_at'] <= 0) {
				$rec['created_at'] = time();
			}
			$rec['expires_at'] = $rec['created_at'] + 86400;
			// $rec['type'] = $url['host'];
			$rec['name'] = trim($src->title ?: $src->link);
			$rec['link'] = trim($src->link ?: $src->comments);
			$rec['source'] = json_encode($src);

			// Would love if this supported ON CONFLICT
			// $dbc->insert('post_incoming', $rec);

			$ret[] = $rec;
		}

		return $ret;
	}

}
