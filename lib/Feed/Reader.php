<?php
/**
 * Feed Reader Wrapper
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed;

class Reader
{
	protected $data;

	protected $feed;

	protected $mime;

	protected $type;

	protected $res; // Response

	protected $url;

	/**
	 *
	 */
	function __construct(string $source)
	{
		if (preg_match('/^http.+/', $source)) {
			// HTTP Source
			$this->type = 'link';
			$this->url = filter_var($source, FILTER_VALIDATE_URL);
			// $this->url = parse_url($this->url);
		// } elseif (preg_match('/^ftp.+/', $source)) {
		// 	// FTP Source?
		// 	$this->type = 'link';
		// 	$url = filter_var($source, FILTER_VALIDATE_URL);
		// 	$url = parse_url($url);
		} elseif (preg_match('/<xml/', $source)) {
			// XML Data
			$this->data = $source;
			$this->type = 'data';
			$this->mime = 'application/xml';
		} elseif (preg_match('/^\{.*\}$/', $source)) {
			// JSON Data
			$this->data = $source;
			$this->type = 'data';
			$this->mime = 'application/json';
		}

	}

	/**
	 *
	 */
	function load()
	{
		switch ($this->type) {
			case 'data':
				// Parse IT
				break;
			case 'link':
				$this->fetch();
		}

		// Need More Mime Refinement?
		switch ($this->mime) {
			case 'application/octet-stream':
			case 'text/plain':
				if (preg_match('/<\?xml/', $this->data)) {
					$this->mime = 'application/xml';
				} elseif (preg_match('/^\{.*\}$/', $this->data)) {
					$this->mime = 'application/json';
				}
				break;
		}

		// XML Type Decipher
		switch ($this->mime) {
			case 'application/xml':
			case 'text/xml':
				$x = new \Edoceo\Rivus\Feed\Reader\XML($this->data);
				$this->mime = $x->getType();
				// $this->data = $x;
				break;
		}

		// Regular Processing
		switch ($this->mime) {
			case 'application/json':
				// JSON
				$this->feed = new \Edoceo\Rivus\Feed\Reader\JSON($this->data);
				break;
			case 'application/atom+xml': // ; charset=UTF-8':
				$this->feed = new \Edoceo\Rivus\Feed\Reader\Atom($this->data);
				// $import_feed = _feed_from_xml_atom($this->data);
				break;
			case 'application/rss+xml':
				$this->feed = new \Edoceo\Rivus\Feed\Reader\RSS($this->data);
				// $import_feed = _feed_from_xml_rss($this->data);
				break;
			case 'text/x-opml':
				$this->feed = new \Edoceo\Rivus\Feed\Reader\OPML($this->data);
				break;
			default:
				echo "  MIME: {$this->mime}\n";
				throw new \Exception('Invalid Feed Source Type');
		}

	}

	/**
	 *
	 */
	function fetch()
	{
		if (empty($this->url)) {
			return null;
		}

		$cfg = [
			// 'base_uri' => $this->_api_base,
			'allow_redirects' => false,
			'cookies' => false,
			'headers' => array(
				'user-agent' => __PHP_USERAGENT,
			),
			'http_errors' => false
		];

		$ghc = new \GuzzleHttp\Client($cfg);
		// $req = new

		$this->res = $ghc->get($this->url);
		// $ret = json_decode($res->getBody(), true);
		// return $ret;

		// $req = __curl_init($this->url);
		// // curl_setopt($req, )
		// $res = curl_exec($req);
		// $this->inf = curl_getinfo($req);

		// $this->mime = strtok($this->inf['content_type'], ';');
		$this->mime = $this->res->getHeaderLine('content-type');
		$this->mime = strtok($this->mime, ';');
		$this->mime = strtolower($this->mime);

		switch ($this->res->getStatusCode()) {
			case 200:
				// OK
				$this->data = $this->res->getBody()->getContents();;
				break;
			case 301:
			case 302:
			case 308:
				$this->data = null;
				$this->mime = null;
		}

	}

	/**
	 *
	 */
	function getInfo()
	{
		$ret = [
			'url' => $this->url,
			'code' => $this->res->getStatusCode(),
			'type' => $this->type,
			'mime' => $this->mime,
		];

		return $ret;

	}

	function getItems()
	{
		if ($this->feed) {
			return $this->feed->getItems();
		}

		return [];
	}

}
