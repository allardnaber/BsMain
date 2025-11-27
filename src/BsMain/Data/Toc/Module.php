<?php /** @noinspection PhpUnused */

namespace BsMain\Data\Toc;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

class Module extends ApiEntity {

	public int $ModuleId;
	public string $Title;
	public int $SortOrder;
	public ?string $StartDateTime; // @todo
	public ?string $EndDateTime; // @todo
	public bool $IsHidden;
	public bool $IsLocked;
	public ?string $PacingStartDate; // @todo
	public ?string $PacingEndDate; // @todo
	public string $DefaultPath;
	public ?string $LastModifiedDate; // @todo
	public RichText $Description; // @missing from documentation

	/** @var Module[] */
	#[ArrayOf(Module::class)]
	public array $Modules;

	/** @var Topic[] */
	#[ArrayOf(Topic::class)]
	public array $Topics;

}
