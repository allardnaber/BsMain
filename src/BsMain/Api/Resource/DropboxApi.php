<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Api\ApiRequest;
use BsMain\Data\Dropbox\DropboxFolder;

/**
 * API endpoints related to Dropboxes (Assignments), {@see https://docs.valence.desire2learn.com/res/dropbox.html}.<br>
 * Completeness: 3 / 36
 */
class DropboxApi extends ApiShell {

	public function deleteDropbox(int $courseId, int $folderId): void {
		$this->client->execute(
			ApiRequest::delete()
				->description('assignment')
				->leUrl('%d/dropbox/folders/%d', $courseId, $folderId)
		);
	}

	/**
	 * @param int $courseId
	 * @param bool|null $onlyCurrentStudentsAndGroups
	 * @return DropboxFolder[]
	 */
	public function getDropboxesByOrgUnit(int $courseId, ?bool $onlyCurrentStudentsAndGroups = null): array {
		return $this->client->fetchArray(DropboxFolder::class,
			ApiRequest::get()
				->description('list of assignments')
				->leUrl('%d/dropbox/folders/', $courseId)
				->param('onlyCurrentStudentsAndGroups', $onlyCurrentStudentsAndGroups)
		);
	}

	public function getDropbox(int $courseId, int $folderId): DropboxFolder {
		return $this->client->fetch(DropboxFolder::class,
			ApiRequest::get()
				->description('assignment')
				->leUrl('%d/dropbox/folders/%d', $courseId, $folderId)
		);
	}
}
