<?php

namespace BsMain\Api;

use BsMain\Data\GroupCategoryDataCreate;
use BsMain\Data\GroupCategoryDataFetch;
use BsMain\Data\GroupCategoryJobStatus;
use BsMain\Data\GroupDataCreate;
use BsMain\Data\GroupDataFetch;
use BsMain\Data\GroupDataUpdate;
use BsMain\Data\GroupEnrollment;
use BsMain\Data\GroupsJobData;

class BsGroupsApi extends BsResourceBaseApi {

	public function getGroupCategory(int $courseId, int $categoryId): GroupCategoryDataFetch {
		return $this->request($this->url('/lp/1.31/%d/groupcategories/%d', $courseId, $categoryId),
			GroupCategoryDataFetch::class, 'the group category');
	}

	/**
	 * @param int $courseId
	 * @return GroupCategoryDataFetch[]
	 */
	public function getGroupCategories(int $courseId): array {
		return $this->requestArray($this->url('/lp/1.31/%d/groupcategories/', $courseId),
			GroupCategoryDataFetch::class, false, 'list of group categories');
	}

	/**
	 * @param int $courseId
	 * @param int $categoryId
	 * @return GroupDataFetch[]
	 */
	public function getGroupsInCategory(int $courseId, int $categoryId): array {
		return $this->requestArray($this->url('/lp/1.31/%d/groupcategories/%d/groups/', $courseId, $categoryId),
			GroupDataFetch::class, false, 'all groups in category');
	}

	public function createGroupCategory(int $courseId, GroupCategoryDataCreate $data): GroupsJobData {
		return $this->request($this->url('/lp/1.31/%d/groupcategories/', $courseId),
			GroupsJobData::class, 'the group category', 'POST', $data->getJson(true));
	}

	public function getGroupCategoryCreationStatus(int $courseId, int $groupCategoryId): GroupCategoryJobStatus {
		return $this->request($this->url('/lp/1.38/%d/groupcategories/%d/status', $courseId, $groupCategoryId),
			GroupCategoryJobStatus::class, 'group category creation status');
	}

	public function getGroup(int $courseId, int $categoryId, int $groupId): GroupDataFetch {
		return $this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/%d', $courseId, $categoryId, $groupId),
			GroupDataFetch::class, 'the group');
	}

	public function createGroup(int $courseId, int $categoryId, GroupDataCreate $data): GroupDataFetch {
		return $this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/', $courseId, $categoryId),
			GroupDataFetch::class, 'the group', 'POST', $data->getJson(true));
	}

	public function updateGroup(int $courseId, int $categoryId, int $groupId, GroupDataUpdate $data): GroupDataFetch {
		return $this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/%d', $courseId, $categoryId, $groupId),
			GroupDataFetch::class, 'the group', 'PUT', $data->getJson(true));
	}

	public function deleteGroup(int $courseId, int $categoryId, int $groupId): void {
		$this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/%d', $courseId, $categoryId, $groupId),
			null, 'the group', 'DELETE');
	}

	public function enrollUserInGroup(int $courseId, int $categoryId, int $groupId, GroupEnrollment $data): void {
		$this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/%d/enrollments/', $courseId, $categoryId, $groupId),
			null, 'the group enrollment', 'POST', $data->getJson(true));
	}

}