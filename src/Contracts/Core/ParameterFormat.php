<?php

namespace Frlnc\Slack\Contracts\Core;

interface ParameterFormat {

	/**
	 * Formats a string for Slack.
	 *
	 * @param  string $parameter
	 * @return string
	 */
	public function format($parameter);

}
