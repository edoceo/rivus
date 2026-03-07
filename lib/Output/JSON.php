<?php
/**
 * JSON Type Output
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Output;

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
