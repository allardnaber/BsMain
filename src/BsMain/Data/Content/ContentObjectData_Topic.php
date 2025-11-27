<?php

namespace BsMain\Data\Content;

use BsMain\Data\Content_T;
use BsMain\Data\Topic_T;

class ContentObjectData_Topic extends ContentObjectData {

	public Topic_T $TopicType;
	public string $Url;
	public ?string $StartDate; // @todo
	public ?string $EndDate; // @todo
	public ?string $DueDate; // @todo
	public ?bool $OpenAsExternalResource;
	public ?bool $MajorUpdate;
	public ?string $MajorUpdateText;
	public ?bool $ResetCompletionTracking;

	public function onCreate(): void {
		$this->Type = Content_T::Topic;
	}

}
