<?php

namespace BsMain\Data\Content;

use BsMain\Data\Content\ActivityType_T;
use BsMain\Data\Topic_T;
use DateTimeInterface;

class ContentObject_Topic extends ContentObject {

	public int $ParentModuleId;
	public Topic_T $TopicType;
	public ?string $Url;

	public ?DateTimeInterface $StartDate;
	public ?DateTimeInterface $EndDate;
	public ?DateTimeInterface $DueDate;
	public bool $IsBroken;
	public ?bool $OpenAsExternalResource;
	public bool $IsExempt;
	public ?int $ToolId;
	public ?int $ToolItemId;
	public ActivityType_T $ActivityType;
	public ?int $GradeItemId;
	/** @var int[] */
	public array $AssociatedGradeItemIds;

}
