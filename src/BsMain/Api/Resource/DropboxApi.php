<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Api\ApiRequest;
use BsMain\Data\Access\UserAccess;
use BsMain\Data\Dropbox\DropboxFolder;
use BsMain\Data\Dropbox\DropboxFolderUpdateData;
use BsMain\Data\OrgUnit\OrgUnitCoreInfo;
use Psr\Http\Message\StreamInterface;

/**
 * API endpoints related to Dropboxes (Assignments), {@see https://docs.valence.desire2learn.com/res/dropbox.html}.<br>
 * Completeness: 8 / 36
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

	/**
	 * Retrieve a list of users with access to the specified dropbox.
	 * @param int $courseId
	 * @param int $folderId
	 * @param int|null $userId Optional. Retrieve access for a single user.
	 * @param int|null $roleId Optional. Retrieve access for users with the given role.
	 * @return UserAccess[]
	 */
	public function getUsersWithDropboxAccess(int $courseId, int $folderId, ?int $userId = null, ?int $roleId = null): array {
		return $this->client->fetchArray(UserAccess::class,
			ApiRequest::get()
				->description('users with access to assignment')
				->leUrl('%d/dropbox/folders/%d/access/', $courseId, $folderId)
				->param('userId', $userId)
				->param('roleId', $roleId)
		);
	}

	public function getDropboxAttachment(int $courseId, int $folderId, int $fileId): StreamInterface {
		$response = $this->client->execute(
			ApiRequest::get()
				->description('attachment for assignment')
				->leUrl('%d/dropbox/folders/%d/attachments/%d', $courseId, $folderId, $fileId)
		);
		return $response->getBody();
	}

	/**'
	 * @param bool $onlyActive
	 * @return OrgUnitCoreInfo[]
	 */
	public function getMyOrgUnitsWithAssessmentRole(bool $onlyActive = false): array {
		return $this->client->fetchArray(OrgUnitCoreInfo::class,
			ApiRequest::get()
				->description('org units where you have the assessment role')
				->leUrl('%d/dropbox/orgUnits/feedback/')
				->param('type', $onlyActive ? 1 : 0)
		);
	}

	public function createDropbox(int $courseId, DropboxFolderUpdateData $dropboxData): DropboxFolder {
		return $this->client->fetch(DropboxFolder::class,
			ApiRequest::post()
				->description('assignment')
				->leUrl('%d/dropbox/folders/', $courseId)
				->jsonBody(json_encode($dropboxData))
		);
	}

	public function updateDropbox(int $courseId, int $folderId, DropboxFolderUpdateData $dropboxData): DropboxFolder {
		return $this->client->fetch(DropboxFolder::class,
			ApiRequest::put()
				->description('assignment')
				->leUrl('%d/dropbox/folders/%d', $courseId, $folderId)
				->jsonBody(json_encode($dropboxData))
		);
	}


}
