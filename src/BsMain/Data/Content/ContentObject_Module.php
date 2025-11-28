<?php

namespace BsMain\Data\Content;

use BsMain\Api\Fields\Attributes\ArrayOf;
use DateTimeInterface;

class ContentObject_Module extends ContentObject {

	/** @var ContentObject[] */
	#[ArrayOf(ContentObject::class)]
	public array $Structure;
	public ?DateTimeInterface $ModuleStartDate;
	public ?DateTimeInterface $ModuleEndDate;
	public ?DateTimeInterface $ModuleDueDate;
	public ?string $Color;
	public ?int $ParentModuleId;
	public ?int $Duration;

}
