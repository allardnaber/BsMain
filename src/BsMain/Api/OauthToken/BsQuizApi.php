<?php

namespace BsMain\Api\OauthToken;

use BsMain\Api\BsResourceBaseApi;
use BsMain\Data\QuizReadData;

class BsQuizApi extends BsResourceBaseApi {

	/**
	 * @param int $courseId
	 * @return QuizReadData[]
	 */
	public function getQuizzes(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/quizzes/', $courseId),
			QuizReadData::class, true, 'list of quizzes');
	}

}
