<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Data\ContentObject;
use BsMain\Data\ContentObjectData;
use BsMain\Data\Toc\TableOfContents;


class ContentApi extends ApiShell {

	public function getCourseToc(int $courseId, ?int $moduleId = null): TableOfContents {
		return $this->client->fetch(
			TableOfContents::get()
			->leUrl('%d/content/toc', $courseId)
			->param('moduleId', $moduleId)
		);

	}

	public function addContentObject(int $courseId, int $parentModuleId, ContentObjectData $contentObject): ContentObject {
		return $this->client->fetch(
			ContentObject::post()
			->leUrl('%d/content/modules/%d/structure/', $courseId, $parentModuleId)
			->jsonBody($contentObject->getJson(true))
		);
	}
}