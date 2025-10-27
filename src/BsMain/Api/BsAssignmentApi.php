<?php

namespace BsMain\Api;

use BsMain\Data\DropboxFolder;

class BsAssignmentApi extends BsResourceBaseApi {

	public function getAssignments(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/dropbox/folders/', $courseId),
			DropboxFolder::class, false, 'list of assignments');
	}


}
