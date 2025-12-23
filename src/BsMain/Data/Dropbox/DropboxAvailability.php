<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\ApiEntity;
use BsMain\Data\Availability_T;
use DateTimeInterface;

class DropboxAvailability extends ApiEntity {
	public ?DateTimeInterface $StartDate;
	public ?DateTimeInterface $EndDate;
	public ?Availability_T $StartDateAvailabilityType;
	public ?Availability_T $EndDateAvailabilityType;
}
