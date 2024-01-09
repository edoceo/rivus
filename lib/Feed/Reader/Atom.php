<?php
/**
 * Atom Feed Reader
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed\Reader;

class Atom extends XML
{
	/**
	 *
	 */
	function getItems()
	{
		$ret = [];

		// var_dump($this->xml->getName());

		// Spin Each Channel
		foreach ($this->xml->entry as $src) {

			$rec = [];
			$rec['id'] = md5(json_encode($src));
			$rec['created_at'] = strtotime($src->published ?: $src->updated); // $src['created'];
			$rec['expires_at'] = $rec['created_at'] + 86400;
			// $rec['type'] = $url['host'];
			$rec['name'] = trim($src->title);
			$rec['link'] = $src->link['href']; // '] ?: $src['comments'];
			$rec['source'] = json_encode($src);

			// Would love if this supported ON CONFLICT
			// $dbc->insert('post_incoming', $rec);

			$ret[] = $rec;
		}

		return $ret;
	}

}
