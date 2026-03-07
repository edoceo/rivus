<?php
/**
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Output;

class RSS
{
	private $body;

	function __construct($body)
	{
		$this->body = $body;
	}

	function render()
	{
		__exit_xml($this->body);
	}
}
