<?php

namespace BsMain\Template;

use SmartyException;

class OutputTemplate extends \Smarty {

	private string $errorTemplate;
	
	public function __construct($config) {
		parent::__construct();
		$this->setPaths($config);
		$this->configLoad($config['defaultLanguage'] . '.conf');
		$this->assign('languageCode', $config['defaultLanguage']);
		$this->errorTemplate = $config['errorTemplate'];
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
	
	public function setLanguage($cultureCode): void {
		try {
			$lang = preg_replace('/[^a-z]/', '', strtolower(substr($cultureCode, 0, 2)));
			$this->configLoad($lang . '.conf');

			// only update _after_ loading config, so it does not get changed if loading fails.
			$this->assign('languageCode', $lang);
		} catch (SmartyException $ex) {
			// The selected language is not available, keep default.
		}
	}

	/**
	 * @throws SmartyException
	 */
	public function displayError(): void {
		$this->display($this->errorTemplate);
	}

}
