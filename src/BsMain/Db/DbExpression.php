<?php

namespace BsMain\Db;

class DbExpression {

	private string $expression;
	public function __construct(string $expression) {
		$this->expression = $expression;
	}

	public function get(): string {
		return $this->expression;
	}
}