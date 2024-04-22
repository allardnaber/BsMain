<?php

namespace BsMain\Api;

use BsMain\Data\ContentObject;
use BsMain\Data\ContentObjectData;
use BsMain\Data\TableOfContents;

class BsContentApi extends BsResourceBaseApi {

	public function getCourseToc(int $courseId, ?int $moduleId = null): TableOfContents {
		return $this->request(
			$moduleId === null
				? $this->url('/le/1.67/%d/content/toc', $courseId)
				: $this->url('/le/1.67/%d/content/toc?moduleId=%d', $courseId, $moduleId),
			TableOfContents::class, 'the table of contents');
	}

	public function addContentObject(int $courseId, int $parentModuleId, ContentObjectData $contentObject): ContentObject {
		return $this->request(
			$this->url('/le/1.67/%d/content/modules/%d/structure/', $courseId, $parentModuleId),
			ContentObject::class, 'the content object', 'POST',
			$contentObject->getJson(true));
	}

	/**
	 * Specify the position order of a content object with respect to its sibling objects.
	 * @param int $courseId Org unit ID.
	 * @param int $objectId Content Object ID.
	 * @param string $position The order position within siblings and can be either 'first', 'last', or the objectId to be placed after.
	 * @return void
	 */
	public function setContentObjectOrder(int $courseId, int $objectId, string $position): void {
		$this->request(
			$this->url('/le/1.67/%d/content/order/objectId/%d?position=%s', $courseId, $objectId, $position),
			null, 'content ordering', 'POST');
	}
}
