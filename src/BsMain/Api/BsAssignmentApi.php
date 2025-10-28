<?php

namespace BsMain\Api;

use BsMain\Api\Util\ResumableFileUploader;
use BsMain\Data\Dropbox\DropboxFeedback;
use BsMain\Data\Dropbox\DropboxFolder;
use BsMain\Data\Dropbox\EntityDropbox;
use BsMain\Exception\BsAppRuntimeException;

class BsAssignmentApi extends BsResourceBaseApi {

	private function validateEntityType(string $entityType): void {
		$lower = strtolower($entityType);
		if ($lower !== 'user' && $lower !== 'group') {
			throw new \InvalidArgumentException(sprintf('%s is not a valid entity type, only "user" and "group" are allowed.', $entityType));
		}
	}

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

	public function addFeedback(int $courseId, int $folderId, string $entityType, int $entityId, DropboxFeedback $feedback): void {
		$this->validateEntityType($entityType);
		$this->request(
			$this->url('/le/1.75/%d/dropbox/folders/%d/feedback/%s/%d', $courseId, $folderId, $entityType, $entityId),
			null, 'assignment feedback', 'POST', $feedback->getJson()
		);
	}

	/**
	 * @param int $courseId
	 * @param int $folderId
	 * @param string $entityType [user|group]
	 * @param int $entityId
	 * @param string $filename The filename to display
	 * @param string $localFileName Local file to upload
	 * @return void
	 */
	public function addFeedbackAttachment(int $courseId, int $folderId, string $entityType, int $entityId, string $filename, string $localFileName): void {
		$this->validateEntityType($entityType);
		$type = mime_content_type($localFileName);
		$uploader = new ResumableFileUploader($this->getClient());
		$uploader->upload(
			$this->url('/le/1.75/%d/dropbox/folders/%d/feedback/%s/%d/upload', $courseId, $folderId, $entityType, $entityId),
			$this->url('/le/1.75/%d/dropbox/folders/%d/feedback/%s/%d/attach', $courseId, $folderId, $entityType, $entityId),
			$filename, $type, $localFileName);
	}

}
