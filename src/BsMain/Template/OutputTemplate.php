<?php

namespace BsMain\Template;

class OutputTemplate extends \Smarty {
	
	private $lang = null;
	private $errorTemplate;
	
	public function __construct($config, $language) {
		parent::__construct();
		$this->setPaths($config);
		$this->configLoad($language . '.conf');
		$this->errorTemplate = $config['errorTemplate'];
	}
	
	private function setPaths($config) {
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
	
	private function createDir($path) {
		if (!file_exists($path)) {
			mkdir ($path, 0770, true);
		}
	}
	
	public function setLanguage($cultureCode) {
		try {
			$this->lang = preg_replace('/[^a-z]/', '', strtolower(substr($cultureCode, 0, 2)));
			$this->configLoad($this->lang . '.conf');
		} catch (\SmartyException $ex) {
			// The selected language is not available, keep default.
		}
	}
	
	public function displayError() {
		$this->display($this->errorTemplate);
	}

}
