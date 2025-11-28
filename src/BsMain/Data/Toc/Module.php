<?php /** @noinspection PhpUnused */

namespace BsMain\Data\Toc;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;
use DateTimeInterface;

class Module extends ApiEntity {

	public int $ModuleId;
	public string $Title;
	public int $SortOrder;
	public ?DateTimeInterface $StartDateTime;
	public ?DateTimeInterface $EndDateTime;
	public bool $IsHidden;
	public bool $IsLocked;
	public ?DateTimeInterface $PacingStartDate;
	public ?DateTimeInterface $PacingEndDate;
	public string $DefaultPath;
	public ?DateTimeInterface $LastModifiedDate;
	public RichText $Description; // @missing from documentation

	/** @var Module[] */
	#[ArrayOf(Module::class)]
	public array $Modules;

	/** @var Topic[] */
	#[ArrayOf(Topic::class)]
	public array $Topics;

}
