<?php
/**
 * JSON Type Output
 */

namespace App\Output;

class JSON
{
	private $body;

	function __construct($body)
	{
		$this->body = $body;
	}

	function render()
	{
		__exit_json($this->body);
	}

}
