<?php

namespace BsMain\Data\Toc;

use BsMain\Data\ActivityType_T;
use BsMain\Data\ApiEntity;
use BsMain\Data\Availability_T;
use BsMain\Data\RichText;

class Topic extends ApiEntity {

	public int $TopicId;
	public int $Identifier;
	public string $TypeIdentifier;
	public string $Title;
	public bool $Bookmarked;
	public bool $Unread;
	public string $Url;
	public int $SortOrder;
	public ?string $StartDateTime;
	public ?string $EndDateTime;
	public ?string $ActivityId;
	public ContentCompletionType_T $CompletionType;
	public bool $IsExempt;
	public bool $IsHidden;
	public bool $IsLocked;
	public bool $IsBroken;
	public ?int $ToolId;
	public ?int $ToolItemId;
	public ActivityType_T $ActivityType;
	public ?int $GradeItemId;
	public ?string $LastModifiedDate;
	public RichText $Description; // missing from documentation
	//public ?Availability_T $StartDateAvailabilityType; // Available in LE's unstable contract
	//public ?Availability_T $EndDateAvailabilityType; // Available in LE's unstable contract

}
