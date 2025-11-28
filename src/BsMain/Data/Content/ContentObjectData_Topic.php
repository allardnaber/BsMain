<?php

namespace BsMain\Data\Content;

use BsMain\Data\Content_T;
use BsMain\Data\Topic_T;
use DateTimeInterface;

class ContentObjectData_Topic extends ContentObjectData {

	public Topic_T $TopicType;
	public string $Url;
	public ?DateTimeInterface $StartDate;
	public ?DateTimeInterface $EndDate;
	public ?DateTimeInterface $DueDate;
	public ?bool $OpenAsExternalResource;
	public ?bool $MajorUpdate;
	public ?string $MajorUpdateText;
	public ?bool $ResetCompletionTracking;

	public function onCreate(): void {
		$this->Type = Content_T::Topic;
	}

}
