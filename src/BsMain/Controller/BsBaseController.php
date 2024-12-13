<?php

namespace BsMain\Controller;

use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use BsMain\Template\OutputTemplate;
use SmartyException;

class BsBaseController {
	
	private OutputTemplate $output;
	private Configuration $config;

	public function __construct(OutputTemplate $output, Configuration $config) {
		$this->output = $output;
		$this->config = $config;
	}

	/**
	 * @throws SmartyException
	 */
	public function display($template): void {
		$this->output->display($template);
	}
	
	public function assign($var, $value): void {
		$this->output->assign($var, $value);
	}
	
	public function getOutput(): OutputTemplate {
		return $this->output;
	}

	public function getFullConfig(): Configuration {
		return $this->config;
	}

	public function getConfig(string ...$path): string|array {
		return $this->config->get(...$path);
	}

	public function getConfigOptional(string ... $path): string|array|null {
		return $this->config->getOptional(...$path);
	}

	protected function requirePOST(): void {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			throw new BsAppRuntimeException('Request was denied, only POST is accepted.');
		}
	}

	
}
