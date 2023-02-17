<?php

namespace BsMain\Api;


use BsMain\Data\GroupEnrollment;
use BsMain\Data\SectionDataCreate;
use BsMain\Data\SectionDataFetch;
use BsMain\Data\SectionSettingsDataCreate;
use BsMain\Data\SectionSettingsDataFetch;
use BsMain\Exception\BsAppApiException;

class BsSectionsApi extends BsResourceBaseApi {

	public function getSectionSettings(int $courseId): ?SectionSettingsDataFetch {
		try {
			return $this->request($this->url('/lp/1.31/%d/sections/settings', $courseId),
				SectionSettingsDataFetch::class, 'section settings');
		} catch (BsAppApiException $ex) {
			if ($ex->getStatusCode() === 404) {
				return null;
			} else {
				throw $ex;
			}
		}
	}

	public function initializeSections(int $courseId, SectionSettingsDataCreate $settings): void {
		$this->request($this->url('/lp/1.39/%d/sections/settings', $courseId),
			null, 'the section settings', 'POST', $settings->getJson(true));
	}

	/**
	 * @param int $courseId
	 * @return SectionDataFetch[]
	 */
	public function getSections(int $courseId): array {
		return $this->requestArray($this->url('/lp/1.31/%d/sections/', $courseId),
			SectionDataFetch::class, false, 'all sections in course');
	}

	public function createSection(int $courseId, SectionDataCreate $data): SectionDataFetch {
		return $this->request($this->url('/lp/1.31/%d/sections/', $courseId),
			SectionDataFetch::class, 'the section', 'POST', $data->getJson(true));
	}

	public function deleteSection($courseId, $sectionId): void {
		$this->request($this->url('/lp/1.31/%d/sections/%d', $courseId, $sectionId),
			null, 'the section', 'DELETE');
	}

	public function enrollUserInSection(int $courseId, int $sectionId, GroupEnrollment $data): void {
		$this->request($this->url('/lp/1.31/%d/sections/%d/enrollments/', $courseId, $sectionId),
			null, 'the section enrollment', 'POST', $data->getJson(true));
	}
}