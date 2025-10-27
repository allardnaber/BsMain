<?php

namespace BsMain\Data\Toc;

use BsMain\Data\ApiEntity;

class TableOfContents extends ApiEntity {

	/**
	 * @var Module[]
	 */
	public array $Modules;

	protected function onCreate(): void {
		$this->Modules = array_map(fn($m) => Module::newInstance($m), $this->Modules);
	}
}

