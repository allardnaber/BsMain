<?php

namespace BsMain\Data\Toc;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;

class TableOfContents extends ApiEntity {

	/** @var Module[] */
	#[ArrayOf(Module::class)]
	public array $Modules;

}
