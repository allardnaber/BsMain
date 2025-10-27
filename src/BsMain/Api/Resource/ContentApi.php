<?php

namespace BsMain\Api\Resource;

use BsMain\Api\Service;
use BsMain\Data\ContentObject;
use BsMain\Data\ContentObjectData;
use BsMain\Data\Toc\TableOfContents;


class ContentApi extends ApiShell {

	public function getCourseToc(int $courseId, ?int $moduleId = null): TableOfContents {
		return TableOfContents::get($this->client)
			->url(Service::le, '%d/content/toc', $courseId)
			->param('moduleId', $moduleId)
			->fetch();
	}

	public function addContentObject(int $courseId, int $parentModuleId, ContentObjectData $contentObject): ContentObject {
		return ContentObject::post($this->client)
			->url(Service::le, '%d/content/modules/%d/structure/', $courseId, $parentModuleId)
			->jsonBody($contentObject->getJson(true))
			->fetch();
	}
}