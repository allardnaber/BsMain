<?php

namespace BsMain\Data\Content;

use BsMain\Api\Fields\Attributes\ArrayOf;

class ContentObject_Module extends ContentObject {

	/** @var ContentObject[] */
	#[ArrayOf(ContentObject::class)]
	public array $Structure;
	public ?string $ModuleStartDate;
	public ?string $ModuleEndDate;
	public ?string $ModuleDueDate;
	public ?string $Color;
	public ?int $ParentModuleId;
	public ?int $Duration;

}
