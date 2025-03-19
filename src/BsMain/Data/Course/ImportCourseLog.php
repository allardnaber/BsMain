<?php

namespace BsMain\Data\Course;

use BsMain\Data\GenericObject;

/**
 * D2L sends:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.GetImportJobResponse
 * {
 * "LogId": <number:D2LID>,
 * "ConversionImportJobId": <number:D2LID>,
 * "OperationTypeId": <string:COI_IMPORTOPERATION_T>,
 * "LogDateTime": <string:UTCDateTime>,
 * "Message": <string>,
 * "TypeId": <string:COI_IMPORTLOGMESSAGE_T>
 * }
 */
class ImportCourseLog extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'LogId', 'ConversionImportJobId', 'OperationTypeId', 'LogDateTime', 'Message', 'TypeId'
		];
	}

	public function getBrightspaceId(): int {
		return $this->LogId;
	}

}
