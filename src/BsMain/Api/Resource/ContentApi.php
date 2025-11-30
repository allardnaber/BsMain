<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Api\ApiRequest;
use BsMain\Api\Util\FileUpload\MultipartFileUploader;
use BsMain\Data\Access\UserAccess;
use BsMain\Data\Content\ContentObject;
use BsMain\Data\Content\ContentObject_Module;
use BsMain\Data\Content\ContentObject_Topic;
use BsMain\Data\Content\ContentObjectData;
use BsMain\Data\Content\ContentObjectData_Module;
use BsMain\Data\Content\ContentObjectData_Topic;
use BsMain\Data\Toc\TableOfContents;
use BsMain\Exception\BrightspaceException;
use Psr\Http\Message\StreamInterface;

/**
 * API endpoints related to Course Offerings, {@see https://docs.valence.desire2learn.com/res/content.html}.<br>
 * Completeness: 14 / 51
 */

class ContentApi extends ApiShell {

	public function deleteModule(int $courseId, int $moduleId): void {
		$this->client->execute(
			ApiRequest::delete()
				->description('module')
				->leUrl('%d/content/modules/%d', $courseId, $moduleId)
		);
	}

	public function deleteTopic(int $courseId, int $topicId): void {
		$this->client->execute(
			ApiRequest::delete()
				->description('topic')
				->leUrl('%d/content/topics/%d', $courseId, $topicId)
		);
	}

	public function getModule(int $courseId, int $moduleId): ContentObject_Module {
		return $this->client->fetch(ContentObject_Module::class,
			ApiRequest::get()
				->description('module')
				->leUrl('%d/content/modules/%d', $courseId, $moduleId)
		);
	}

	/**
	 * Retrieve a list of users with access to a specified module.
	 * @param int $courseId
	 * @param int $moduleId
	 * @param int|null $userId Optional. Retrieve access for a single user.
	 * @param int|null $roleId Optional. Retrieve access for users with the given role.
	 * @return UserAccess[]
	 */
	public function getModuleUserAccess(int $courseId, int $moduleId, ?int $userId = null, ?int $roleId = null): array {
		return $this->client->fetchArray(UserAccess::class,
			ApiRequest::get()
				->description('module user access')
				->leUrl('%d/content/modules/%d/access/', $courseId, $moduleId)
				->param('userId', $userId)
				->param('roleId', $roleId)
		);
	}

	/**
	 * @param int $courseId
	 * @param int $moduleId
	 * @return ContentObject[]
	 */
	public function getModuleStructure(int $courseId, int $moduleId): array {
		return $this->client->fetchArray(ContentObject::class,
			ApiRequest::get()
				->description('module structure')
				->leUrl('%d/content/modules/%d/structure/', $courseId, $moduleId)
		);
	}

	/**
	 * Retrieve the root module(s) for an org unit.
	 * @param int $courseId
	 * @return ContentObject_Module[]
	 */
	public function getRootModules(int $courseId): array {
		return $this->client->fetchArray(ContentObject_Module::class,
			ApiRequest::get()
				->description('course root modules')
				->leUrl('%d/content/root/', $courseId)
		);
	}

	public function getTopic(int $courseId, int $topicId): ContentObject_Topic {
		return $this->client->fetch(ContentObject_Topic::class,
			ApiRequest::get()
				->description('topic')
				->leUrl('%d/content/topics/%d', $courseId, $topicId)
		);
	}

	/**
	 * Retrieve a list of users with access to a specified content topic.
	 * @param int $courseId
	 * @param int $topicId
	 * @param int|null $userId Optional. Retrieve access for a single user.
	 * @param int|null $roleId Optional. Retrieve access for users with the given role.
	 * @return UserAccess[]
	 */
	public function getTopicUserAccess(int $courseId, int $topicId, ?int $userId = null, ?int $roleId = null): array {
		return $this->client->fetchArray(UserAccess::class,
			ApiRequest::get()
				->description('topic user access')
				->leUrl('%d/content/topics/%d/access/', $courseId, $topicId)
				->param('userId', $userId)
				->param('roleId', $roleId)
		);
	}

	/**
	 * Retrieve the content topic file for a content topic.
	 * @param int $courseId
	 * @param int $topicId
	 * @return StreamInterface
	 */
	public function getTopicFile(int $courseId, int $topicId): StreamInterface {
		$response = $this->client->execute(
			ApiRequest::get()
				->description('content topic file contents')
				->leUrl('%d/content/topics/%d/file', $courseId, $topicId)
		);
		return $response->getBody();
	}

	/* **** @todo Exemptions  */

	/**
	 * Create topic or module
	 * @todo for file topics a specialized procedure is required to include the file contents.
	 * @param int $courseId
	 * @param int $parentModuleId
	 * @param ContentObjectData $contentObject
	 * @param bool $renameFileIfExists
	 * @return ContentObject
	 */
	public function createContentObject(int $courseId, int $parentModuleId, ContentObjectData $contentObject,
										bool $renameFileIfExists = false): ContentObject {
		return $this->client->fetch(ContentObject::class,
			ApiRequest::post()
				->description('content object')
				->leUrl('%d/content/modules/%d/structure/', $courseId, $parentModuleId)
				->param('renameFileIfExists', $renameFileIfExists)
				->jsonBody(json_encode($contentObject))
		);
	}

	public function createRootModule(int $courseId, ContentObjectData_Module $moduleData): ContentObject_Module {
		return $this->client->fetch(ContentObject_Module::class,
			ApiRequest::post()
				->description('root module')
				->leUrl('%d/content/root/', $courseId)
				->jsonBody(json_encode($moduleData))
		);
	}

	public function updateModule(int $courseId, int $moduleId, ContentObjectData_Module $moduleData): void {
		$this->client->execute(
			ApiRequest::put()
				->description('module')
				->leUrl('%d/content/modules/%d', $courseId, $moduleId)
				->jsonBody(json_encode($moduleData))
		);
	}

	public function updateTopic(int $courseId, int $topicId, ContentObjectData_Topic $topicData): void {
		$this->client->execute(
			ApiRequest::put()
				->description('topic')
				->leUrl('%d/content/topics/%d', $courseId, $topicId)
				->jsonBody(json_encode($topicData))
		);
	}

	public function updateTopicFile(int $courseId, int $topicId, string $localFileName): void {
		$request = ApiRequest::put()
			->description('topic file')
			->leUrl('%d/content/topics/%d/file', $courseId, $topicId);
		MultipartFileUploader::addFile($request, 'file', $localFileName);
		$this->client->execute($request);
	}

	public function getCourseToc(int     $courseId,
								 ?bool   $ignoreDateRestrictions = null,
								 ?bool   $ignoreModuleDateRestrictions = null,
								 ?int    $userId = null,
								 ?int    $moduleId = null,
								 ?string $title = null
	): TableOfContents {
		return $this->client->fetch(TableOfContents::class,
			ApiRequest::get()
				->leUrl('%d/content/toc', $courseId)
				->param('ignoreDateRestrictions', $ignoreDateRestrictions)
				->param('ignoreModuleDateRestrictions', $ignoreModuleDateRestrictions)
				->param('userId', $userId)
				->param('moduleId', $moduleId)
				->param('title', $title)
		);
	}

	/**
	 * Specify the position order of a content object with respect to its sibling objects.
	 * Note that you can only use this action to arrange the order of content objects in a single generation (that is,
	 * sibling content objects all with the same parent content module, or all at the root level).
	 * @param int $courseId
	 * @param int $objectId
	 * @param string|int $position 'first', 'last' or the objectId to be placed after.
	 * @return void
	 */
	public function setContentObjectOrder(int $courseId, int $objectId, string|int $position): void {
		$positionVal = strtolower($position);
		if (!preg_match('/^(first|last|[0-9]+)$/', $positionVal)) {
			throw new BrightspaceException(sprintf(
				'Invalid value "%s" for position: should be "first", "last" or the id of the object to follow.',
				$position)
			);
		}
		$this->client->execute(
			ApiRequest::post()
				->description('content object order')
				->leUrl('%d/content/order/objectId/%d', $courseId, $objectId)
				->param('position', $positionVal)
		);
	}



}