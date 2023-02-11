<?php

namespace BsMain\Controller;

use BsMain\Template\OutputTemplate;
use SmartyException;

class BsBaseController {
	
	private OutputTemplate $output;
	private array $config;

	public function __construct(OutputTemplate $output, array $config) {
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

	public function getConfig(): array {
		return $this->config;
	}
	

	
}
