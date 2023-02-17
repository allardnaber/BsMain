<?php

namespace BsMain\Configuration;

class VaultSecret {

	private string $key;

	public function __construct(string $key) {
		$this->key = $key;
	}

	public function getKey(): string {
		return $this->key;
	}
}
