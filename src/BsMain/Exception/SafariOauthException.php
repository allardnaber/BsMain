<?php

namespace BsMain\Exception;

/**
 * Exception thrown when a Safari users wants to connect through OAuth2.
 * This is impossible, as Safari blocks first-party cookies. This mechanism can be used to intercept these
 * events and ensure that the page is opened in a new window.
 */
class SafariOauthException extends \RuntimeException {

	public const EXCEPTION_PARAM_NAME = 'safariRedirect';

	public function __construct(string $message = "") {
		$url = $_SERVER['REQUEST_URI'];
		$htmlUrl = htmlentities(
			sprintf('%s%s%s=true', $url, str_contains($url, '?') ? '&' : '?', self::EXCEPTION_PARAM_NAME)
		);
		parent::__construct(
			sprintf('Within Safari you can only use the tool if you open it in a new window.<br>' .
		 			'<a href="%s" target="_blank">Proceed to the tool</a>', $htmlUrl)
		);
	}

}
