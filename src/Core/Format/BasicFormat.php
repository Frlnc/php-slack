<?php

namespace Frlnc\Slack\Core\Format;

use Frlnc\Slack\Contracts\Core\ParameterFormat;

class Basic implements ParameterFormat {

	public function format($string)
	{
		$string = str_replace('&', '&amp;', $string);
		$string = str_replace('<', '&lt;', $string);
		$string = str_replace('>', '&gt;', $string);

		return $string;
	}

}
