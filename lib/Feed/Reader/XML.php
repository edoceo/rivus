<?php
/**
 * XML Feed Reader
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed\Reader;

class XML
{
	protected $type;

	private $valid = false;

	private $xml;

	/**
	 *
	 */
	function __construct(string $source)
	{
		// $er = error_reporting(0);

		// Try SimpleXML
		$this->xml = null;
		libxml_use_internal_errors(true);
		try {
			$this->xml = new \SimpleXMLElement($source);
		} catch (\Exception $e) {
			$this->xml = null;
			echo "  ATOM: Failed SimpleXMLElement\n";
			$err_list = libxml_get_errors();
			foreach ($err_list as $err) {
				var_dump($err);
			}
			// echo "SOURCE: $source\n";
		}
		libxml_clear_errors();
		libxml_use_internal_errors(false);

		libxml_use_internal_errors(true);

		if (empty($this->xml)) {
			$dom = null;
			try {
				$dom = new \DOMDocument('1.0','UTF-8');
				$dom->preserveWhiteSpace = false;
				$dom->strictErrorChecking = false;
				$dom->validateOnParse = false;
				$dom->loadHTML($source);
				$valid = true;
			} catch (\Exception $e) {
				$dom = null;
				echo "  ATOM: Failed DOMDocument\n";
				$err_list = libxml_get_errors();
				foreach ($err_list as $err) {
					var_dump($err);
				}
			}

			$this->xml = simplexml_import_dom($dom);

		}

		libxml_clear_errors();
		libxml_use_internal_errors(false);

		// error_reporting($er);

		if ( ! empty($this->xml)) {
			$this->valid = true;
		}

		// Type?
		$node0 = $this->xml->getName();
		switch ($node0) {
			case 'feed':
				$this->type = 'application/atom+xml';
				break;
			case 'opml':
				$this->type = 'application/opml+xml';
				$this->type = 'text/x-opml';
				break;
			case 'rss':
				$this->type = 'application/rss+xml';
				break;
		}

		// $ns_list = $this->xml->getDocNamespaces();
		// if (count($ns_list) > 1) {
		// 	var_dump($ns_list); exit;
		// }

	}

	/**
	 *
	 */
	function getType()
	{
		return $this->type;
	}

}
