<?php

namespace BsMain\Template;

use Smarty;
use SmartyException;

class OutputTemplate extends Smarty {

	private string $errorTemplate;
	private string $errorSafariTemplate;

	public function __construct($config) {
		parent::__construct();
		$this->setPaths($config);
		$this->configLoad($config['defaultLanguage'] . '.conf');
		$this->errorTemplate = $config['errorTemplate'];
		$this->errorSafariTemplate = $config['errorSafariTemplate'] ?? $config['errorTemplate'];
	}
	
	private function setPaths($config): void {
		$base = $config['baseDir'];
		$cacheDir = $base . $config['cacheDir'];
		$compileDir = $base . $config['compileDir'];
		$this->createDir($cacheDir);
		$this->createDir($compileDir);
		$this->setTemplateDir($base);
		$this->setConfigDir($base . $config['configDir']);
		$this->setCacheDir($cacheDir);
		$this->setCompileDir($compileDir);
		$this->escape_html = true;
	}
	
	private function createDir($path): void {
		if (!file_exists($path)) {
			mkdir ($path, 0770, true);
		}
	}

	/** @noinspection PhpRedundantCatchClauseInspection implementation is dynamically loaded, exception will be thrown
	 * @noinspection PhpUnused
	 */
	public function setLanguage($cultureCode): void {
		try {
			$lang = preg_replace('/[^a-z]/', '', strtolower(substr($cultureCode, 0, 2)));
			$this->configLoad($lang . '.conf');
		} catch (SmartyException) {
			// Preferred language is not available, falls back to default (english).
		}
	}

	/**
	 * @throws SmartyException
	 */
	public function displayError(): void {
		$this->display($this->errorTemplate);
	}

	/**
	 * @throws SmartyException
	 */
	public function displaySafariError(): void {
		$this->display($this->errorSafariTemplate);
	}

}
