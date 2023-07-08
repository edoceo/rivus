<?php
/**
 *
 */

namespace Edoceo\Rivus\Output;

class HTML
{
	private $body;
	private $name;

	/**
	 *
	 */
	function __construct($body)
	{
		$this->body = $body;
	}

	/**
	 *
	 */
	function render()
	{

		header('cache-control: no-store,max-age=0');

		ob_start();
		require_once(__DIR__ . '/HTML/Body.php');
		return ob_get_clean();
	}

	/**
	 *
	 */
	function setPageTitle($x)
	{
		$this->name = $x;
	}
}
