<?php

namespace BsMain\Api;

use BsMain\Data\Dropbox\DropboxFolder;
use BsMain\Data\Dropbox\EntityDropbox;

class BsAssignmentApi extends BsResourceBaseApi {

	/**
	 * @param int $courseId
	 * @return DropboxFolder[]
	 */
	public function getAssignments(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/dropbox/folders/', $courseId),
			DropboxFolder::class, false, 'list of assignments');
	}

	/**
	 * @param int $courseId
	 * @param int $folderId
	 * @param bool $activeOnly Optional. Include only submissions from actively enrolled users.
	 * @return EntityDropbox[]
	 */
	public function getSubmissions(int $courseId, int $folderId, bool $activeOnly = false): array {
		$postfix = $activeOnly ? '?activeOnly=true' : '';
		return $this->requestArray(
			$this->url('/le/1.75/%d/dropbox/folders/%d/submissions/paged/%s', $courseId, $folderId, $postfix),
			EntityDropbox::class, true, 'list of submissions');
	}

}
