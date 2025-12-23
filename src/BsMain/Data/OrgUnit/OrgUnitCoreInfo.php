<?php

namespace BsMain\Data\OrgUnit;

use BsMain\Data\ApiEntity;
use DateTimeInterface;

class OrgUnitCoreInfo extends ApiEntity {
	public int $Identifier;
	public string $TypeIdentifier;
	public string $Name;
	public ?string $Code;
	public string $Path;
	public bool $IsActive;
	public ?DateTimeInterface $StartDate;
	public ?DateTimeInterface $EndDate;
}
