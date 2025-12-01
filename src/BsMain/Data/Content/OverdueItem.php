<?php

namespace BsMain\Data\Content;

use BsMain\Data\ApiEntity;
use DateTimeInterface;

class OverdueItem extends ApiEntity {

	public int $UserId;
	public int $OrgUnitId;
	public int $ItemId;
	public string $ItemName;
	public DateTimeInterface $DueDate;

}
