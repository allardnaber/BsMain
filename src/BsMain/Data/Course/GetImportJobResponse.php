<?php

namespace BsMain\Data\Course;

use BsMain\Data\ApiEntity;

/**
 * {@see https://docs.valence.desire2learn.com/res/course.html#Course.GetImportJobResponse}
 */
class GetImportJobResponse extends ApiEntity {

	public string $JobToken;
	public int $TargetOrgUnitId;
	public Coi_ImportJobStatus_T $Status;
	
}
