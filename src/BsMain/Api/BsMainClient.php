<?php

namespace BsMain\Api;

use BsMain\Data\SectionSettingsDataCreate;
use BsMain\Exception\BsAppApiException;

class BsMainClient extends BsApiClient {
	
	public function getCourseOffering($courseId) {
		$response = $this->request($this->url('/lp/1.31/courses/%d', $courseId), 'the course');
		return \BsMain\Data\CourseOffering::create($response);
	}
	
	public function getMyLocale() {
		$response = $this->request($this->url('/lp/1.31/accountSettings/mySettings/locale/'), 'your preferred language');
		return \BsMain\Data\Locale::create($response);
	}
	
	public function getUser(string $username) {
		$response = $this->request($this->url('/lp/1.33/users/?username=%s', $username), 'the user');
		return \BsMain\Data\UserData::create($response);
	}
	
	public function getUserEnrollmentsForTypeAndRole(int $userId, $orgUnitType, $role) {
		$response = $this->requestPaged($this->url('/lp/1.31/enrollments/users/%d/orgUnits/?orgUnitTypeId=%d&roleId=%d', $userId, $orgUnitType, $role), 'user enrollments');
		return \BsMain\Data\UserOrgUnit::createArray($response, true);
	}
	
	public function getOrgUnitAncestorByType(int $orgUnit, $orgUnitType) {
		$response = $this->request($this->url('/lp/1.31/orgstructure/%d/ancestors/?ouTypeId=%d', $orgUnit, $orgUnitType), 'the ancestor');
		return \BsMain\Data\OrgUnit::createArray($response, false);
	}
	
	public function getUsersByOrgUnit(int $orgUnit) {
		$response = $this->requestPaged($this->url('/le/1.51/%d/classlist/paged/', $orgUnit), 'enrolled users');
		return \BsMain\Data\ClasslistUser::createArray($response, true);
	}
	
	public function getRolesInOrgUnit(int $orgUnit) {
		$response = $this->request($this->url('/lp/1.31/%d/roles/', $orgUnit), 'available roles');
		return \BsMain\Data\Role::createArray($response);
	}
	
	public function getGroupCategory(int $courseId, int $categoryId) {
		$response = $this->request($this->url('/lp/1.31/%d/groupcategories/%d', $courseId, $categoryId), 'the group category');
		return \BsMain\Data\GroupCategoryDataFetch::create($response);
	}
	
	public function getGroupCategories(int $courseId) {
		$response = $this->request($this->url('/lp/1.31/%d/groupcategories/', $courseId), 'list of group categories');
		return \BsMain\Data\GroupCategoryDataFetch::createArray($response);
	}
	
	public function getGroupsInCategory(int $courseId, int $categoryId) {
		$response = $this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/', $courseId, $categoryId), 'all groups in category');
		return \BsMain\Data\GroupDataFetch::createArray($response);
	}
	
	public function createGroupCategory(int $courseId, \BsMain\Data\GroupCategoryDataCreate $data) {
		$response = $this->request($this->url('/lp/1.31/%d/groupcategories/', $courseId), 'the group category', 'POST', $data->getJson(true));
		return \BsMain\Data\GroupsJobData::create($response);
	}
	
	public function getGroupCategoryCreationStatus(int $courseId, int $groupCategoryId) {
		$response = $this->request($this->url('/lp/1.38/%d/groupcategories/%d/status', $courseId, $groupCategoryId), 'group category creation status');
		return \BsMain\Data\GroupCategoryJobStatus::create($response);
	}
	
	public function createGroup(int $courseId, int $categoryId, \BsMain\Data\GroupDataCreate $data) {
		$response = $this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/', $courseId, $categoryId), 'the group', 'POST', $data->getJson(true));
		return \BsMain\Data\GroupDataFetch::create($response);		
	}

	public function createSection(int $courseId, \BsMain\Data\SectionDataCreate $data) {
		$response = $this->request($this->url('/lp/1.31/%d/sections/', $courseId), 'the section', 'POST', $data->getJson(true));
		return \BsMain\Data\SectionDataFetch::create($response);
	}

	public function deleteGroup(int $courseId, int $categoryId, int $groupId) {
		$this->request($this->url('/lp/1.31/%d/groupcategories/%d/groups/%d', $courseId, $categoryId, $groupId), 'the group', 'DELETE');
	}

	public function enrollUserInGroup(int $courseId, int $categoryId, int $groupId, \BsMain\Data\GroupEnrollment $data) {
		$this->request(
				$this->url('/lp/1.31/%d/groupcategories/%d/groups/%d/enrollments/', $courseId, $categoryId, $groupId),
				'the group enrollment', 'POST', $data->getJson(true));
	}

	public function enrollUserInSection(int $courseId, int $sectionId, \BsMain\Data\GroupEnrollment $data) {
		$this->request(
			$this->url('/lp/1.31/%d/sections/%d/enrollments/', $courseId, $sectionId),
			'the section enrollment', 'POST', $data->getJson(true));
	}

	public function getSectionSettings(int $courseId) {
		try {
			$response = $this->request($this->url('/lp/1.31/%d/sections/settings', $courseId), 'section settings');
		}
		catch (BsAppApiException $ex) {
			if ($ex->getStatusCode() === 404) {
				return null;
			} else {
				throw $ex;
			}
		}
		return \BsMain\Data\SectionSettingsDataFetch::create($response);
	}

	public function initializeSections(int $courseId, SectionSettingsDataCreate $settings) {
		$this->request(
			$this->url('/lp/1.39/%d/sections/settings', $courseId),
			'the section settings', 'POST', $settings->getJson(true));
	}

	public function getSections(int $courseId) {
		$response = $this->request($this->url('/lp/1.31/%d/sections/', $courseId), 'all sections in course');
		return \BsMain\Data\SectionDataFetch::createArray($response);
	}

	public function deleteSection($courseId, $sectionId) {
		$this->request($this->url('/lp/1.31/%d/sections/%d', $courseId, $sectionId), 'the section', 'DELETE');
	}

}
