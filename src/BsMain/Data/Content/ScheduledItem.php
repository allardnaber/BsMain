<?php

namespace BsMain\Data\Content;

use BsMain\Data\ApiEntity;
use BsMain\Data\Content_T;
use DateTimeInterface;

/**
 * A scheduled item describes an activity in an org unit that was added to the content tool with start date, end date,
 * or due date.
 * {@see https://docs.valence.desire2learn.com/res/content.html#Content.ScheduledItem}
 */
class ScheduledItem extends ApiEntity {

	public int $UserId;
	public int $OrgUnitId;
	public int $ItemId;
	public string $ItemName;
	public Content_T $ItemType;
	public ?string $ItemUrl;
	public ?DateTimeInterface $StartDate;
	public ?DateTimeInterface $EndDate;
	public ?DateTimeInterface $DueDate;
	public ContentCompletionType_T $CompletionType;
	public ?DateTimeInterface $DateCompleted;
	public ActivityType_T $ActivityType;
	public bool $IsExempt;

}
