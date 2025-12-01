<?php

namespace BsMain\Data\Content;

use BsMain\Data\ApiEntity;

/**
 * The count of content items with a start, end, or due date, for the provided org unit context.
 */
class ScheduledItemCount extends ApiEntity {

	public int $OrgUnitId;
	public int $UserId;
	public int $ItemCount;

}
