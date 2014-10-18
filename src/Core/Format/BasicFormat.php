<?php

namespace Frlnc\Slack\Core\Format;

use Frlnc\Slack\Contracts\Core\ParameterFormat;

class Basic implements ParameterFormat {

	public function format($string)
	{
		$src = array(
			'\\&'       => '||amp||',
			'\\<'       => '||lt||',
			'\\>'       => '||gt||',
			'&'         => '&amp;',
			'<'         => '&lt;',
			'>'         => '&gt;',
			'||amp||'   => '&',
			'||lt||'    => '<',
			'||gt||'    => '>'
		);

		$string = str_replace(array_keys($src), array_values($src), $string);

		return $string;
	}

}
