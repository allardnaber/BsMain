<?php

namespace BsMain\Data;

use ReflectionClass;

/**
 * D2L returns:
 * see ???  Values are undocumented
 */
class CoiImportJobStatus_T extends GenericObject {
	const Uploading = 'UPLOADING';
	const ReadyToImportNatively = 'READYTOIMPORTNATIVELY';
	const Importing = 'IMPORTING';
	const ImportFailed = 'IMPORTFAILED';
	const Completed = 'COMPLETED';
	const Timeout = 'Timeout';
	const Unknown = 'UNKNOWN';

	protected function getAvailableFields() {
		return [ 'Status' ];
	}

}
