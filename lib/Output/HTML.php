<?php
/**
 *
 */

namespace Edoceo\Rivus\Output;

class HTML
{
	private $body;
	private $name;

	function __construct($body)
	{
		$this->body = $body;
	}

	function render()
	{
		ob_start();
		require_once(__DIR__ . '/HTML-template.php');
		return ob_get_clean();
	}

	function setPageTitle($x)
	{
		$this->name = $x;
	}
}
